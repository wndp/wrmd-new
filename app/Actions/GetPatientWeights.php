<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Enums\Entity;
use App\Events\GettingPatientWeights;
use App\Models\CareLog;
use App\Models\Exam;
use App\Models\Patient;
use App\Summarizable;
use App\Support\WeightsCollection;
use App\Weighable;
use Illuminate\Support\Fluent;

class GetPatientWeights
{
    use AsAction;

    /**
     * Handle the event.
     */
    public static function handle(Patient $patient)
    {
        $records = event(new GettingPatientWeights($patient));
        $records[] = CareLog::where('patient_id', $patient->id)->get();
        $records[] = Exam::where('patient_id', $patient->id)->get();

        return (new WeightsCollection($records))
            ->collapse()
            ->filter(function ($model) {
                return $model instanceof Weighable && $model instanceof Summarizable;
            })
            ->map(function ($model) {
                return new Fluent([
                    'weighed_at_date_time' => $model->{$model->summary_date},
                    'weighed_at_date' => $model->{$model->summary_date}->toDateString(),
                    'weighed_at_formated' => $model->{$model->summary_date}->toFormattedDateString(),
                    'weighed_at_timestamp' => $model->{$model->summary_date}->timestamp,
                    'type' => Entity::tryFrom($model->getTable())->label(),
                    'weight' => $model->summary_weight,
                    'unit_id' => $model->summary_weight_unit_id,
                ]);
            })
            ->where('weight', '>', 0)
            ->sortBy('weighed_at_timestamp', SORT_NUMERIC)
            ->values();
    }
}
