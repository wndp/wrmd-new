<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Patients\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PatientClassificationController extends Controller
{
    /**
     * Return a list of a patients current classifications.
     */
    public function __invoke(Patient $patient, string $category): JsonResponse
    {
        $patient->validateOwnership(Auth::user()->currentAccount->id);

        return response()->json(
            $patient->predictions->where('category', Str::studly($category))->values()
        );
    }
}
