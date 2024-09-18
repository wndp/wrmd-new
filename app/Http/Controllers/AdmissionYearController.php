<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdmissionYearController extends Controller
{
    /**
     * Return miscellaneous details about the admissions in a given year.
     */
    public function __invoke(string $year): JsonResponse
    {
        return response()->json([
            'year' => (int) $year,
            'last_case_id' => Admission::getLastCaseId(Auth::user()->current_team_id, $year),
        ]);
    }
}
