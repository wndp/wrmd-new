<?php

namespace App\Http\Controllers\Research;

use App\Domain\Patients\Patient;
use App\Extensions\Research\Models\Banding;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BandingController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(settings('timezone'))->startOfDay();

        $request->validate([
            'band_number' => 'required',
            'banded_at' => 'required|date|after_or_equal:'.$admittedAt,
        ], [
            'banded_at.required' => 'The banding date field is required.',
            'banded_at.date' => 'The banding date is not a valid date.',
            'banded_at.after_or_equal' => 'The banding date must be a date after or equal to '.$admittedAt->toFormattedDateString().'.',
        ]);

        $banding = Banding::firstOrNew(['patient_id' => $patient->id]);
        $banding->patient_id = $patient->id;
        $banding->fill($request->only([
            'band_number',
            'banded_at',
            'age_code',
            'how_aged',
            'sex_code',
            'how_sexed',
            'status_code',
            'additional_status_code',
            'band_size',
            'master_bander_id',
            'banded_by',
            'location_id',
            'band_disposition',
            'remarks',
        ]));
        $banding->save();

        return back();
    }
}
