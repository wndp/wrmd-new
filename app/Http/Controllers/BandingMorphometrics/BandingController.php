<?php

namespace App\Http\Controllers\BandingMorphometrics;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveBandingRequest;
use App\Models\Banding;
use App\Models\Patient;

class BandingController extends Controller
{
    public function __invoke(SaveBandingRequest $request, Patient $patient)
    {
        $banding = Banding::firstOrNew(['patient_id' => $patient->id]);
        $banding->fill($request->only([
            'band_number',
            'banded_at',
            'age_code_id',
            'how_aged_id',
            'sex_code_id',
            'how_sexed_id',
            'status_code_id',
            'additional_status_code_id',
            'band_size_id',
            'master_bander_number',
            'banded_by',
            'location_number',
            'band_disposition_id',
            'remarks',
        ]));
        $banding->save();

        return back();
    }
}
