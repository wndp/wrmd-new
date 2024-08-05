<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use App\Events\PatientUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MatanYadaev\EloquentSpatial\Objects\Point;

class IntakeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id)
            ->update($request->validate([
                'transported_by' => 'nullable',
                'admitted_by' => 'required',
                'found_at' => 'required|date',
                'address_found' => 'required',
                'city_found' => 'required',
                'county_found' => 'nullable',
                'subdivision_found' => 'required',
                // 'lat_found' => when(settings('showGeolocationFields'), 'nullable|numeric|between:-90,90')
                // 'lng'_found => when(settings('showGeolocationFields'), 'nullable|numeric|between:-180,180')
                'reasons_for_admission' => 'required',
                'care_by_rescuer' => 'nullable',
                'notes_about_rescue' => 'nullable',
            ], [
                'lat_found.between' => 'The latitude must be between -90 and 90',
                'lng_found.between' => 'The longitude must be between -180 and 180',
            ]));

        if ($request->filled('lat_found', 'lng_found')) {
            $patient::$disableGeoLocation = true;
            $patient->coordinates_found = new Point($request->lat_found, $request->lng_found);
            $patient->save();
        }

        event(new PatientUpdated($patient));

        return back();
    }
}
