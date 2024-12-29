<?php

namespace App\Http\Controllers\BandingMorphometrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAuxiliaryMarkerRequest;
use App\Models\Banding;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;

class AuxiliaryMarkerController extends Controller
{
    public function __invoke(SaveAuxiliaryMarkerRequest $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);

        Banding::updateOrCreate(['patient_id' => $patient->id], $request->only([
            'auxiliary_marker',
            'auxiliary_marker_color_id',
            'auxiliary_side_of_bird_id',
            'auxiliary_marker_type_id',
            'auxiliary_marker_code_color_id',
            'auxiliary_placement_on_leg_id',
        ]));

        return back();
    }
}
