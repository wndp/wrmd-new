<?php

namespace App\Http\Controllers\Research;

use App\Domain\Patients\Patient;
use App\Extensions\Research\Models\Morphometric;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MorphometricsController extends Controller
{
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(settings('timezone'))->startOfDay();

        $request->validate([
            'measured_at' => 'required|date|after_or_equal:'.$admittedAt,
        ], [
            'measured_at.required' => 'The date measured field is required.',
            'measured_at.date' => 'The date measured is not a valid date.',
            'measured_at.after_or_equal' => 'The date measured must be a date after or equal to '.$admittedAt->toFormattedDateString().'.',
        ]);

        $morphometric = Morphometric::firstOrNew(['patient_id' => $patient->id]);
        $morphometric->patient_id = $patient->id;
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
