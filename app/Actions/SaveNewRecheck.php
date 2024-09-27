<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Recheck;
use App\Support\DailyTasksFilters;
use Carbon\Carbon;

class SaveNewRecheck
{
    use AsAction;

    public function handle(string $patientId, array $data)
    {
        $recheck = new Recheck([
            'recheck_start_at' => Carbon::parse($data['recheck_start_at']),
            'recheck_end_at' => isset($data['recheck_end_at']) ? Carbon::parse($data['recheck_end_at']) : null,
            'frequency_id' => $data['frequency_id'],
            'assigned_to_id' => $data['assigned_to_id'],
            'description' => $data['description'],
            'patient_id' => $patientId,
        ]);

        [$singleDoseId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::DAILY_TASK_FREQUENCIES->value,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE->value
        ]);

        if ($data['frequency_id'] === $singleDoseId && empty($data['recheck_end_at'])) {
            $recheck->recheck_end_at = Carbon::parse($data['recheck_start_at']);
        }

        $recheck->save();

        return $recheck;
    }
}
