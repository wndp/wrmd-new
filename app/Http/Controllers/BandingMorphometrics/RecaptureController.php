<?php

namespace App\Http\Controllers\BandingMorphometrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBandingRecaptureRequest;
use App\Models\Banding;
use App\Models\Patient;

class RecaptureController extends Controller
{
    public function __invoke(SaveBandingRecaptureRequest $request, Patient $patient)
    {
        Banding::updateOrCreate(['patient_id' => $patient->id], $request->only([
            'recaptured_at',
            'recapture_disposition_id',
            'present_condition_id',
            'how_present_condition_id',
        ]));

        return back();
    }
}
