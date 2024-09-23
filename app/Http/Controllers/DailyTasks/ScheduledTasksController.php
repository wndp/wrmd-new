<?php

namespace App\Http\Controllers\DailyTasks;

use App\Domain\DailyTasks\DailyTaskOptions;
use App\Domain\DailyTasks\Prescriptions\Prescription;
use App\Domain\DailyTasks\Rechecks\Recheck;
use App\Enums\DailyTaskSchedulable;
use App\Http\Controllers\Controller;
use App\Support\Wrmd;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class ScheduledTasksController extends Controller
{
    public function edit(Request $request): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        $tasks = Collection::make(DailyTaskSchedulable::cases())
            ->map(
                fn ($schedulable) =>
                $schedulable->model()::where('patient_id', $admission->patient_id)
                    ->with('recordedTasks.user')
                    ->get()
            )
            ->collapse()
            ->transform(fn ($schedulable) => [
                ...$schedulable->toArray(),
                'type' => Wrmd::uriKey($schedulable),
                'type_id' => $schedulable->id,
                'patient_id' => $schedulable->patient_id,
                'start_date' => $schedulable->start_date,
                'end_date' => $schedulable->end_date,
                'badge_color' => $schedulable->badge_color,
                'badge_text' => $schedulable->badge_text,
                'summary_body' => $schedulable->summary_body,
                'occurrences' => $schedulable->occurrences,
                'recorded_tasks' => $schedulable->recordedTasks,
            ])
            ->sortBy([
                ['created_at', Wrmd::settings('logOrder') === 'desc' ? 'desc' : 'asc'],
                ['start_date', Wrmd::settings('logOrder') === 'desc' ? 'desc' : 'asc'],
            ])
            ->values();


        // $tasks[] = Recheck::where('patient_id', $admission->patient_id)
        //     ->with('recordedTasks.user')
        //     ->get();

        // $tasks[] = Prescription::where('patient_id', $admission->patient_id)
        //     ->with('recordedTasks.user')
        //     ->get();

        // $tasks = collect($tasks)
        //     ->collapse()
        //     ->each->append('type')
        //     ->sortBy([
        //         fn ($task) => $task->start_date->timestamp, SORT_NUMERIC, (settings('logOrder') === 'desc'),
        //         fn ($task) => $task->created_at->timestamp, SORT_NUMERIC, (settings('logOrder') === 'desc'),
        //     ])
        //     ->values()
        //     ->dd();

        return Inertia::render('Patients/ScheduledTasks', compact('tasks'));
    }
}
