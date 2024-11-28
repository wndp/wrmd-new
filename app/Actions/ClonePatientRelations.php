<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Models\Patient;
use Illuminate\Support\Collection;

class ClonePatientRelations
{
    use AsAction;

    public function handle(Patient $rootPatient, Patient $newPatient)
    {
        $rootPatient->relations = [];

        $rootPatient->load([
            'banding',
            'careLogs',
            'exams',
            'locations',
            'morphometric',
            'prescriptions',
            'rechecks',
            'necropsy',
            'nutritionPlans',
            'oilWaterproofingAssessments',
            'oilProcessing',
            'labReports'
        ]);

        foreach ($rootPatient->getRelations() as $relationName => $collectionOrModel) {
            $relation = [];

            foreach (Collection::wrap($collectionOrModel) as $model) {
                $relation[] = $model->replicate(['id', 'patient_id', 'is_abnormal_finding']);
            }

            $newPatient->{$relationName}()->saveMany($relation);
        }

        $rootPatient->getMedia()->each(fn ($media) => $media->copy($newPatient));
    }
}
