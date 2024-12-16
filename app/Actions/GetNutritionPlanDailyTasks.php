<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Enums\DailyTaskSchedulable;
use App\Models\NutritionPlan;
use App\Support\DailyTasksFilters;

class GetNutritionPlanDailyTasks
{
    use AsAction;

    public static function handle(DailyTasksFilters $filters, array $patientIds)
    {
        if (! in_array(DailyTaskSchedulable::NUTRITION->value, $filters->include)) {
            return;
        }

        return NutritionPlan::with('ingredients')
            ->select('nutrition_plans.*')
            ->whereIn('patient_id', $patientIds)
            ->where('started_at', '<=', $filters->date->format('Y-m-d'))
            ->where(
                fn ($query) => $query->whereNull('ended_at')
                    ->orWhere('ended_at', '>=', $filters->date->format('Y-m-d'))
            )
            ->with('patient.admissions')
            ->get();
        //->filter->isDueOn($filters->date);
    }
}
