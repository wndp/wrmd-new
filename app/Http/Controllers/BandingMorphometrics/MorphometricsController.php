<?php

namespace App\Http\Controllers\BandingMorphometrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBandingMorphometricsRequest;
use App\Models\Morphometric;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MorphometricsController extends Controller
{
    public function __invoke(SaveBandingMorphometricsRequest $request, Patient $patient)
    {
        $morphometric = Morphometric::firstOrNew(['patient_id' => $patient->id]);
        $morphometric->fill($request->only([
            'measured_at',
            'bill_length',
            'bill_width',
            'bill_depth',
            'head_bill_length',
            'culmen',
            'exposed_culmen',
            'wing_chord',
            'flat_wing',
            'tarsus_length',
            'middle_toe_length',
            'toe_pad_length',
            'hallux_length',
            'tail_length',
            'weight',
            'samples_collected',
            'remarks',
        ]));
        $morphometric->save();

        return back();
    }
}
