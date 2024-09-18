<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Enums\DailyTaskSchedulable;
use App\Models\Prescription;
use App\Support\DailyTasksFilters;

class GetPrescriptionDailyTasks
{
    use AsAction;

    /**
     * Handle the event.
     */
    public static function handle(DailyTasksFilters $filters, array $patientIds)
    {
        if (!in_array(DailyTaskSchedulable::PRESCRIPTIONS->value, $filters->include)) {
            return;
        }

        return Prescription::select('prescriptions.*')
            ->whereIn('patient_id', $patientIds)
            ->where('rx_started_at', '<=', $filters->date->format('Y-m-d'))
            ->where(
                fn ($query) => $query->whereNull('rx_ended_at')
                    ->orWhere('rx_ended_at', '>=', $filters->date->format('Y-m-d'))
            )
            ->with('patient.admissions')
            ->get()
            ->filter->isDueOn($filters->date);
    }
}
