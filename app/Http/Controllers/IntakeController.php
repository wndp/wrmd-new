<?php

namespace App\Http\Controllers;

use App\Enums\SettingKey;
use App\Events\PatientUpdated;
use App\Models\Patient;
use App\Support\Wrmd;
use App\ValueObjects\SingleStorePoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IntakeController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $request->validate([
            'transported_by' => 'nullable',
            'admitted_by' => 'required',
            'found_at' => 'required|date',
            'address_found' => 'required',
            'city_found' => 'required',
            'county_found' => 'nullable',
            'subdivision_found' => 'required',
            'lat_found' => Rule::when(Wrmd::settings(SettingKey::SHOW_GEOLOCATION_FIELDS), 'nullable|required_with:lng_found|numeric|between:-90,90'),
            'lng_found' => Rule::when(Wrmd::settings(SettingKey::SHOW_GEOLOCATION_FIELDS), 'nullable|required_with:lat_found|numeric|between:-180,180'),
            'reason_for_admission' => 'required',
            'care_by_rescuer' => 'nullable',
            'notes_about_rescue' => 'nullable',
        ], [
            'lat_found.between' => 'The latitude must be between -90 and 90',
            'lng_found.between' => 'The longitude must be between -180 and 180',
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id)
            ->update($request->only([
                'transported_by',
                'admitted_by',
                'found_at',
                'address_found',
                'city_found',
                'county_found',
                'subdivision_found',
                'lat_found',
                'lng_found',
                'reason_for_admission',
                'care_by_rescuer',
                'notes_about_rescue',
            ]));

        if ($request->filled('lat_found', 'lng_found')) {
            $patient->coordinates_found = new SingleStorePoint($request->lat_found, $request->lng_found);
            $patient->save();
        }

        event(new PatientUpdated($patient));

        return back();
    }
}
