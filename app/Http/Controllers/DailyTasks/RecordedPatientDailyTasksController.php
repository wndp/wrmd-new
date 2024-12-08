<?php

namespace App\Http\Controllers\DailyTasks;

use App\Concerns\GetSchedulableFromResource;
use App\Events\DailyTaskCompleted;
use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Collections\DailyTasksCollection;
use App\Support\DailyTasksFilters;
use App\Support\Timezone;
use App\Wrmd;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RecordedPatientDailyTasksController extends Controller
{
    use GetSchedulableFromResource;

    /**
     * Store a new daily-tasks task check in storage.
     */
    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'completed_at' => 'nullable|date',
            'filters' => 'required|array',
            'filters.date' => 'required|date',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        DailyTasksCollection::make()
            ->withFilters(
                new DailyTasksFilters($request->filters)
            )
            ->forPatient($patient, Auth::user()->currentTeam)
            ->each(function ($task) use ($request) {
                $schedualableModel = $this->getSchedulable($task['type'], $task['type_id']);

                $occurrences = Collection::range(1, $task['number_of_occurrences'])
                    ->diff($task['completed_occurrences'])
                    ->each(function ($occurrence) use ($request, $schedualableModel) {
                        // $check = new DailyTask([
                        //     'summary' => $schedualableModel->summary_body,
                        //     'occurrence' => $occurrence,
                        //     'occurrence_at' => $request->input('filters.date'),
                        //     'completed_at' => $request->convertDateFromLocal('completed_at'),
                        // ]);

                        $attributes = [
                            'occurrence' => $occurrence,
                            'occurrence_at' => $request->input('filters.date'),
                        ];

                        $values = [
                            'user_id' => Auth::id(),
                            'summary' => $schedualableModel->summary_body,
                            'completed_at' => Timezone::convertFromLocalToUtc($request->input('completed_at'))
                        ];


                        $schedualableModel->recordedTasks()->updateOrCreate($attributes, $values);
                        //event(new DailyTaskCompleted($schedualableModel, $check));
                    });
            });
    }

    /**
     * Remove a daily-tasks task check from storage.
     */
    public function destroy(Request $request, Patient $patient)
    {
        $request->validate([
            'filters' => 'required|array',
            'filters.date' => 'required|date',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        DailyTasksCollection::make()
            ->withFilters(
                new DailyTasksFilters($request->filters)
            )
            ->forPatient($patient, Auth::user()->currentTeam)
            ->each(function ($task) use ($request) {
                $this->getSchedulable($task['type'], $task['type_id'])
                    ->recordedTasks()
                    ->where('occurrence_at', $request->input('filters.date'))
                    ->delete();
            });
    }
}
