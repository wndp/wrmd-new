<?php

namespace App\Enums;

use App\Actions\GetNutritionPlanDailyTasks;
use App\Actions\GetPrescriptionDailyTasks;
use App\Actions\GetRecheckDailyTasks;
use App\Models\NutritionPlan;
use App\Models\Prescription;
use App\Models\Recheck;
use App\Support\Wrmd;

enum DailyTaskSchedulable: string
{
    case RECHECKS = 'RECHECKS';
    case PRESCRIPTIONS = 'PRESCRIPTIONS';
    case NUTRITION = 'NUTRITION';

    public function label(): string
    {
        return match ($this) {
            self::RECHECKS => 'Rechecks',
            self::PRESCRIPTIONS => 'Prescriptions',
            self::NUTRITION => 'Nutrition',
        };
    }

    public function action(): string
    {
        return match ($this) {
            self::RECHECKS => GetRecheckDailyTasks::class,
            self::PRESCRIPTIONS => GetPrescriptionDailyTasks::class,
            self::NUTRITION => GetNutritionPlanDailyTasks::class,
        };
    }

    public function model(): string
    {
        return match ($this) {
            self::RECHECKS => Recheck::class,
            self::PRESCRIPTIONS => Prescription::class,
            self::NUTRITION => NutritionPlan::class,
        };
    }

    public static function tryFromUriKey($model)
    {
        return match (Wrmd::uriKey($model)) {
            'recheck' => self::RECHECKS,
            'prescription' => self::PRESCRIPTIONS,
            'nutrition-plan' => self::NUTRITION
        };
    }
}
