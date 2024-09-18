<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Classifications\Prediction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ClassificationsDatasetController extends Controller
{
    /**
     * Return a dataset for machine learning for the provided category.
     */
    public function __invoke(string $category): JsonResponse
    {
        $category = Str::studly($category);

        $dataSet = $this->predictionsFor($category)
            ->with('patient')
            ->get()
            ->groupBy('patient.id')
            ->transform(function ($predictions, $patientId) use ($category) {
                $data = [
                    'patient_id' => $patientId,
                    'text' => $predictions->first()->patient->{$category},
                    'terms' => $predictions->pluck('prediction'),
                ];

                // Better to use https://docs.spatie.be/laravel-query-builder/v2/introduction/
                // but for now just hard code this.
                if (request('include') === 'patient' && request()->has('fields')) {
                    $data['patient'] = \Illuminate\Support\Arr::only($predictions->first()->patient->toArray(), request('fields'));
                }

                return $data;
            })
            ->shuffle()
            ->values();

        return response()->json($dataSet);
    }

    private function predictionsFor($category)
    {
        $class = app("App\Domain\Classifications\\$category");

        return Prediction::where('category', $category)
            ->where('accuracy', 1)
            ->where('is_validated', true)
            ->whereIn('prediction', $class::officialTerms());
    }
}
