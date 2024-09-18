<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Patients\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PatientPredictionsController extends Controller
{
    /**
     * Return a paginated list of the latest predictions.
     */
    public function __invoke(Patient $patient, string $category): JsonResponse
    {
        try {
            $response = app('App\Domain\Classifications\\'.Str::studly($category).'ClassificationGateway')->handle($patient->{$category});
        } catch (\Exception $e) {
            return response()->json([]);
        }

        $response['data'] = (new Collection($response['data']))
            ->sortByDesc(function ($accuracy) {
                return $accuracy;
            })
            ->map(function ($accuracy) {
                return round($accuracy * 100, 4).'%';
            });

        return response()->json($response);
    }
}
