<?php

namespace App\Http\Controllers\Research;

use App\Domain\Patients\Patient;
use App\Extensions\Research\Models\Banding;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecaptureController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);
        $admittedAt = $patient->admitted_at->setTimezone(settings('timezone'))->startOfDay();

        $request->validate([
            'recaptured_at' => 'required|date|after_or_equal:'.$admittedAt,
        ], [
            'recaptured_at.required' => 'The recapture date field is required.',
            'recaptured_at.date' => 'The recapture date is not a valid date.',
            'recaptured_at.after_or_equal' => 'The recapture date must be a date after or equal to '.$admittedAt->toFormattedDateString().'.',
        ]);

        $banding = Banding::firstOrNew(['patient_id' => $patient->id]);
        $banding->patient_id = $patient->id;
        $banding->fill($request->only([
            'recaptured_at',
            'recapture_disposition',
            'present_condition',
            'how_present_condition',
        ]));
        $banding->save();

        return back();
    }
}
