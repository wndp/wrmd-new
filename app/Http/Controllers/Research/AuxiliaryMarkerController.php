<?php

namespace App\Http\Controllers\Research;

use App\Domain\Patients\Patient;
use App\Extensions\Research\Models\Banding;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuxiliaryMarkerController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'auxiliary_marker' => 'required',
        ]);

        $banding = Banding::firstOrNew(['patient_id' => $patient->id]);
        $banding->patient_id = $patient->id;
        $banding->fill($request->only([
            'auxiliary_marker',
            'auxiliary_marker_color',
            'auxiliary_side_of_bird',
            'auxiliary_marker_type',
            'auxiliary_marker_code_color',
            'auxiliary_placement_on_leg',
        ]));
        $banding->save();

        return back();
    }
}
