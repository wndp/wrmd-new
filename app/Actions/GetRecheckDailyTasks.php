<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Enums\DailyTaskSchedulable;
use App\Models\Recheck;
use App\Support\DailyTasksFilters;

class GetRecheckDailyTasks
{
    use AsAction;

    public static function handle(DailyTasksFilters $filters, array $patientIds)
    {
        if (! in_array(DailyTaskSchedulable::RECHECKS->value, $filters->include)) {
            return;
        }

        return Recheck::select('rechecks.*')
            ->whereIn('patient_id', $patientIds)
            // ->where('recheck_start_at', '<=', $filters->date->format('Y-m-d'))
            // ->where(
            //     fn ($query) => $query->whereNull('recheck_end_at')
            //         ->orWhere('recheck_end_at', '>=', $filters->date->format('Y-m-d'))
            // )
            ->with('patient.admissions')
            ->get()
            ->filter->isDueOn($filters->date);
    }
}
