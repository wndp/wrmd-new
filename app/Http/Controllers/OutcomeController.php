<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use App\Domain\Patients\PatientOptions;
use App\Events\PatientUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MatanYadaev\EloquentSpatial\Objects\Point;

class OutcomeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param    $Patient $patient
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $admittedAt = $patient->admitted_at->setTimezone(settings('timezone'))->startOfDay();

        $patient->validateOwnership(Auth::user()->current_account_id)
            ->update($request->validate([
                'disposition' => 'required|in:'.implode(',', PatientOptions::$dispositions),
                'dispositioned_at' => 'nullable|required_unless:disposition,Pending|date|after_or_equal:'.$admittedAt,
                'release_type' => 'nullable|in:'.implode(',', PatientOptions::$releaseTypes),
                'transfer_type' => 'nullable|in:'.implode(',', PatientOptions::$transferTypes),
                'disposition_address' => 'nullable',
                'disposition_city' => 'nullable',
                'disposition_subdivision' => 'nullable',
                'disposition_postal_code' => 'nullable',
                'reason_for_disposition' => 'nullable',
                'dispositioned_by' => 'nullable',
                'carcass_saved' => 'nullable|boolean',
            ], [
                'dispositioned_at.required_unless' => 'The disposition date field is required unless disposition is in Pending.',
                'dispositioned_at.date' => 'The disposition date is not a valid date.',
                'dispositioned_at.after_or_equal' => 'The disposition date must be a date after or equal to '.$admittedAt->toFormattedDateString().'.',
            ]));

        if ($request->filled('disposition_lat', 'disposition_lng')) {
            $patient::$disableGeoLocation = true;
            $patient->disposition_coordinates = new Point($request->disposition_lat, $request->disposition_lng);
            $patient->save();
        }

        event(new PatientUpdated($patient));

        return back();
    }
}
