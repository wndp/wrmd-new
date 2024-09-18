<?php

namespace App\Http\Controllers\Necropsy;

use App\Domain\Patients\Patient;
use App\Extensions\Necropsy\Necropsy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsyMorphometricsController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill($request->only([
            'weight',
            'weight_unit',
            'age',
            'age_unit',
            'sex',
            'bcs',
            'wing',
            'tarsus',
            'culmen',
            'exposed_culmen',
            'bill_depth',
        ]));
        $necropsy->save();

        return back();
    }
}
