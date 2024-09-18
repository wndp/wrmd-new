<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Admissions\Admission;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MisidentifiedPatientController extends Controller
{
    /**
     * Get all of the unrecognized patients.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            Admission::select('admissions.*')
                ->joinPatients()
                ->whereMisidentified()
                ->orderBy('common_name')
                ->with('patient', 'account')
                ->paginate(500)
        );
    }
}
