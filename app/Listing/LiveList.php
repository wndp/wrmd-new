<?php

namespace App\Listing;

use App\Caches\PatientSelector;
use App\Enums\Attribute;
use App\Enums\Entity;
use App\Enums\SettingKey;
use App\Models\Team;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use JsonSerializable;

abstract class LiveList implements JsonSerializable
{
    protected $year;

    protected $columns;

    protected $request;

    /**
     * Constructor.
     */
    public function __construct(public Team $team)
    {
        $this->team = $team;

        $this->columns = $team->settingsStore()->get(SettingKey::LIST_FIELDS, [
            Attribute::PATIENT_LOCATIONS_FACILITY_ID->value,
            Attribute::PATIENTS_BAND->value,
            Attribute::PATIENTS_DISPOSITION_ID->value,
            Attribute::PATIENTS_DISPOSITIONED_AT->value,
        ]);
    }

    /**
     * String-ify the report.
     *
     * @return array
     */
    public function __toString()
    {
        return $this->jsonSerialize();
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return Wrmd::humanize($this);
    }

    /**
     * Get the icon of the report.
     */
    public function icon(): string
    {
        return '';
    }

    /**
     * Return the data/cases to be displayed in the list.
     *
     * @return \Illuminate\Support\Collection | \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    abstract public function data();

    /**
     * Set the columns to select.
     *
     * @param  string|array  $columns
     * @return $this
     */
    public function select($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Set the year to restrict the list to.
     *
     * @param  string|int  $year
     * @return $this
     */
    public function year($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Set the request data.
     *
     * @return $this
     */
    public function withRequest(Request $request)
    {
        $this->request = new Fluent($request->all());

        return $this;
    }

    /**
     * Get the reports key. Used to generate links to the report.
     *
     * @return string
     */
    public function key()
    {
        return Wrmd::uriKey($this);
    }

    public function get()
    {
        $headers = $this->formatListHeaders();
        $rows = $this->formatListResults();
        $selected = PatientSelector::get();

        return compact('rows', 'headers', 'selected');
    }

    public function normalizeRows($admissions)
    {
        return $admissions
            ->map(function ($admission) {
                $row['patient_id'] = $admission->patient_id;
                $row['url'] = Wrmd::patientRoute($admission);
                $row['case_number'] = $admission->case_number;
                $row['admitted_at'] = $admission->patient->date_admitted_at->format(config('wrmd.date_format'));
                $row['common_name'] = $admission->patient->common_name;

                foreach ($this->columns as $tableColumn) {
                    [$table, $column] = explode('.', $tableColumn);
                    $entity = Entity::tryFrom($table);
                    $patientRelationship = $entity->patientRelationshipName();

                    if ($entity === Entity::PATIENT) {
                        $model = $admission->patient;
                    } elseif ($entity->shouldDisplayLatestInLists()) {
                        $model = $admission->patient->{$patientRelationship}->last(
                            fn ($model) => $model->created_at //
                        );
                    } else {
                        dd('what is this model?', $entity);
                    }

                    $rawValue = $model?->$column;

                    if (is_null($rawValue)) {
                        $row[$tableColumn] = null;

                    } elseif (Str::endsWith($column, '_at') || $rawValue instanceof Carbon) {
                        $row[$tableColumn] = Carbon::parse($rawValue)->format(config('wrmd.date_format'));

                    } elseif (Attribute::tryFrom($tableColumn)?->hasAttributeOptions()) {
                        $row[$tableColumn] = $model->{Attribute::from($tableColumn)->attributeOptionOwningModelRelationship()}?->value;

                    } else {
                        $row[$tableColumn] = $rawValue;

                    }
                }

                return $row;
            })
            ->values();
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'title' => $this->title(),
            'icon' => $this->icon(),
            'key' => $this->key(),
        ];
    }

    /**
     * Format the list's headers to translated values.
     */
    public function formatListHeaders(): array
    {
        return array_map(function ($name) {
            $attribute = Attribute::tryFrom($name);

            return [
                'label' => $attribute->label(),
                'key' => $name,
            ];
        }, $this->columns);
    }

    /**
     * Format the list's results to be presented.
     *
     * @return array
     */
    private function formatListResults()
    {
        $admissions = $this->data();
        $data = $this->normalizeRows($admissions);

        return $this->normalizePagination($admissions, $data);

        //return compact('pagination');
    }

    private function normalizePagination($admissions, $data)
    {
        if ($admissions instanceof LengthAwarePaginator) {
            return array_merge($admissions->toArray(), compact('data'));
            // return [
            //     'total' => $cases->total(),
            //     'per_page' => $cases->perPage(),
            //     'current_page' => $cases->currentPage(),
            //     'last_page' => $cases->lastPage(),
            //     'from' => $cases->firstItem(),
            //     'to' => $cases->lastItem(),
            // ];
        }

        return array_merge(compact('data'), [
            'total' => $admissions->count(),
        ]);

        // return [
        //     'total' => $cases->count(),
        //     //'per_page' => $cases->count(),
        //     // 'current_page' => 1,
        //     // 'last_page' => 1,
        //     // 'from' => 1,
        //     // 'to' => 1,
        // ];
    }
}
