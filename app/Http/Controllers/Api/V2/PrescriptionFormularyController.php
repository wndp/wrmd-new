<?php

namespace App\Http\Controllers\Api\V2;

use App\Enums\FormulaType;
use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionFormularyController extends Controller
{
    /**
     * Search the formulary.
     */
    public function __invoke(Request $request, Patient $patient = null): JsonResponse
    {
        $request->validate([
            'search' => 'required|string'
        ]);

        if ($patient) {
            $patient->validateOwnership(Auth::user()->current_team_id);
        }

        $formulas = Formula::whereIn('team_id', [
            Auth::user()->current_team_id,
            Auth::user()->currentTeam->isSubAccount() ? Auth::user()->currentTeam->master_account_id : 0
        ])->where('type', FormulaType::PRESCRIPTION->value)
            ->search($request->input('search'))
            ->get()
            ->map(fn ($formula) => [
                ...$formula->toArray(),
                'calculated' => $formula->calculatedAttributes($patient)
            ])
            ->sortBy(fn ($formula) => Str::lower($formula['name']))
            ->values();

        return response()->json($formulas);
    }
}
