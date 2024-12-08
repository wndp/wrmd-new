<?php

namespace App\Collections;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\DailyTaskSchedulable;
use App\Enums\SettingKey;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\PatientLocation;
use App\Models\Team;
use App\Schedulable;
use App\Support\DailyTasksFilters;
use App\Support\Wrmd;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DailyTasksCollection extends Collection
{
    private $filters;

    private $tasks;

    private $team;

    private $patientIds;

    private static $patientParts = [
        'patient_id', 'case_number', 'common_name', 'identity', 'url', 'area', 'enclosure',
    ];

    public function __construct($items = [])
    {
        $this->filters = new DailyTasksFilters();

        parent::__construct($items);
    }

    /**
     * Set the daily tasks filters.
     */
    public function withFilters(DailyTasksFilters $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Collect the potential patients who might have daily tasks.
     */
    public function forTeam(Team $team): Collection
    {
        $this->team = $team;

        $query = Patient::select('patients.id')
            ->join('admissions', 'patients.id', '=', 'admissions.patient_id')
            ->where('team_id', $team->id);

        if ($this->filters->facility === 'anywhere') {
            $query->leftJoinCurrentLocation()->whereNotNull('facility_id');
        } elseif ($this->filters->facility === 'none-assigned') {
            $query->leftJoinCurrentLocation()->whereNull('facility_id');
        } else {
            $query->leftJoinCurrentLocation()->where('facility_id', $this->filters->facility);
        }

        if (!$this->filters->include_non_pending) {
            [$dispositionPendingId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
            ]);

            $query->where('patients.disposition_id', $dispositionPendingId);
        }

        if (!$this->filters->include_non_possession) {
            $query->where('team_possession_id', $team->id);
        }

        $this->patientIds = $query->pluck('id');

        return $this->getNormalizedTasks();
    }

    /**
     * Collect the patient to get its daily tasks.
     */
    public function forPatient(Patient $patient, Team $team): Collection
    {
        $this->team = $team;
        $this->patientIds = new Collection($patient->id);

        return data_get(
            $this->getNormalizedTasks(),
            '0.patients.0.tasks',
            new self()
        );
    }

    /**
     * Get the daily tasks for the pre-qualified patients.
     */
    public function getTasksForDate(): Collection
    {
        $patientIds = $this->patientIds->toArray();

        return self::make(array_map(
            fn ($schedulable) => $schedulable->action()::run($this->filters, $patientIds),
            DailyTaskSchedulable::cases()
        ))->collapse();
    }

    /**
     * Get and normalize the daily tasks.
     */
    public function getNormalizedTasks(): Collection
    {
        $this->tasks = $this->getTasksForDate()
            ->filter(function ($model) {
                return $model instanceof Schedulable;
            })

            // Make sure relations are eager loaded
            ->each->load('recordedTasks', 'patient.admissions')

            // Map models into a dailyTask object.
            ->map(function ($model) {
                return $this->dailyTask($model, $this->filters->date);
            })

            // Sort tasks chronologically.
            ->sortBy('timestamp', SORT_NUMERIC);

        return $this->groupAndParseTasks();
    }

    /**
     * Get task that are 7 days past due.
     */
    public function getPastDueForPatient(Patient $patient, Team $team): Collection
    {
        $this->team = $team;
        $this->patientIds = new Collection($patient->id);

        $timezone = $team->settingsStore()->get(SettingKey::TIMEZONE);

        return Collection::make(
            new CarbonPeriod(
                Carbon::now($timezone)->subDays(7),
                Carbon::now($timezone)->subDays(1)
            )
        )->transform(function ($date) {
            $this->filters->date = $date;

            return [
                'date' => $date,
                'taskGroup' => $this->getNormalizedTasks(),
            ];
        })
        ->filter(function ($dateGroup) {
            return $dateGroup['taskGroup']->filter(function ($locationGroup) {
                return $locationGroup['patients']->filter(function ($patient) {
                    return $patient['tasks']->filter(function ($task) {
                        return $task['number_of_occurrences'] > $task['completed_occurrences']->count();
                    })
                        ->isNotEmpty();
                })
                    ->isNotEmpty();
            })
                ->isNotEmpty();
        })
        ->map(function ($dateGroup) {
            return [
                'date' => $dateGroup['date'],
                'tasks' => data_get(
                    $dateGroup,
                    'taskGroup.0.patients.0.tasks',
                    []
                ),
            ];
        })
        ->values();
    }

    /**
     * Group, filter and format tasks according to the provided filters.
     */
    private function groupAndParseTasks(): Collection
    {
        // Group the task according to the provided filters.
        // FYI: groupBy needs access to the entire tasks collection.
        return $this->tasks->groupBy([function ($task) {
            return $this->groupByFilter($task);
        }, 'patient_id'])

        // Filter by slug
            ->filter(function ($groupedByFilter, $category) {
                if ($this->filters->get('slug')) {
                    return $this->filters->slug === Str::slug("$category-tasks");
                }

                return true;
            })

        // Map and parse the data into non associative arrays.
        // Better for JavaScript loops.
            ->map(function ($groupedByFilter, $category) {
                $patients = $groupedByFilter->map(function ($patientIdGroup) {
                    $patient = Arr::only($patientIdGroup->first(), static::$patientParts);

                    $tasks = $patientIdGroup->map(function ($task) {
                        return Arr::except($task, static::$patientParts);
                    });

                    return array_merge($patient, ['tasks' => $tasks]);
                })
                    ->values();

                $slug = Str::slug("$category-tasks");

                return compact('patients', 'category', 'slug');
            })

        // Sort groups by category
            ->sortBy(function ($group) {
                return mb_strtolower($group['category']);

                // return $this->filters->groupBy === 'assigned_to'
                //     ? array_search($key, $assignments)
                //     : mb_strtolower($key);
            })
            ->values();
    }

    public function dailyTask($model, Carbon $date)
    {
        $admission = Admission::custody($this->team, $model->patient);
        $lastLocation = $model->patient->locations->first();

        return [
            'type' => Wrmd::uriKey($model),
            'type_id' => $model->id,
            'badge_text' => $model->badge_text,
            'badge_color' => $model->badge_color,
            'timestamp' => $model->{$model->summary_date}->timestamp,
            'patient_id' => $model->patient_id,
            'url' => $admission->url,
            'case_number' => $admission->case_number,
            'common_name' => $model->patient->common_name,
            'identity' => implode(', ', array_filter([
                $model->patient->band, $model->patient->name, $model->patient->reference_number,
            ])),
            'number_of_occurrences' => $model->occurrences,
            'completed_occurrences' => $model->recordedTasks()
                ->where('occurrence_at', $date->format('Y-m-d'))
                ->whereNotNull('completed_at')
                ->orderBy('occurrence_at')
                ->pluck('occurrence'),
            'incomplete_occurrences' => $model->recordedTasks()
                ->where('occurrence_at', $date->format('Y-m-d'))
                ->whereNull('completed_at')
                ->orderBy('occurrence_at')
                ->pluck('occurrence'),
            'body' => nl2br($model->summary_body),
            'area' => $lastLocation?->area,
            'enclosure' => "{$lastLocation?->area} {$lastLocation?->enclosure}",
            'assignment' => $model->assigned_to,
            'model' => $model->toArray(),
        ];
    }

    /**
     * Callback to group tasks by provided filters.
     */
    public function groupByFilter(array $task): string
    {
        if ($this->filters->group_by === 'Area') {
            return $task['area'] ?? __('Unknown');
        }

        if ($this->filters->group_by === 'Enclosure') {
            return $task['enclosure'] ?? __('Unknown');
        }

        if ($this->filters->group_by === 'Type') {
            return $this->deriveType($task);
        }
    }

    /**
     * Derive the tasks type.
     */
    public function deriveType(array $task): string
    {
        if ($task['type'] === 'recheck') {
            return __('Recheck').': '.$task['assignment'];
        }

        return Str::ucfirst($task['type']);
    }

    /**
     * Get the potential patients who might have daily tasks.
     */
    public function patientIds(): Collection
    {
        return $this->patientIds;
    }
}
