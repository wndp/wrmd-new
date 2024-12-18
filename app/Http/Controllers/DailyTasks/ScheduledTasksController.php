<?php

namespace App\Http\Controllers\DailyTasks;

use App\Enums\DailyTaskSchedulable;
use App\Enums\SettingKey;
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
                fn ($schedulable) => $schedulable->model()::where('patient_id', $admission->patient_id)
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
                ['created_at', Wrmd::settings(SettingKey::LOG_ORDER) === 'desc' ? 'desc' : 'asc'],
                ['start_date', Wrmd::settings(SettingKey::LOG_ORDER) === 'desc' ? 'desc' : 'asc'],
            ])
            ->values();

        return Inertia::render('Patients/ScheduledTasks', compact('tasks'));
    }
}
