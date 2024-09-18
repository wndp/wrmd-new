<?php

namespace App\Http\Controllers\Necropsy;

use App\Domain\Patients\Patient;
use App\Extensions\Necropsy\Necropsy;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NecropsyCarcassController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'is_previously_frozen' => 'nullable|boolean',
            'is_scavenged' => 'nullable|boolean',
            'is_discarded' => 'nullable|boolean',
        ]);

        $necropsy = Necropsy::firstOrNew(['patient_id' => $patient->id]);
        $necropsy->patient_id = $patient->id;
        $necropsy->fill($request->only([
            'carcass_condition',
            'is_previously_frozen',
            'is_scavenged',
            'is_discarded',
        ]));
        $necropsy->save();

        return back();
    }
}
