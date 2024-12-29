<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Models\Location;
use App\Models\Patient;
use App\Models\PatientLocation;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatientLocationsController extends Controller
{
    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        $request->validate([
            'moved_in_at' => [
                'required',
                'date',
                'after_or_equal:'.$admittedAt,
            ],
            'facility_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::PATIENT_LOCATION_FACILITIES),
            ],
            'area' => 'required_without:hash',
            'hash' => [
                Rule::exists('locations', 'hash')->where(function ($query) {
                    $query->where('team_id', Auth::user()->current_team_id);
                }),
            ],
        ]);

        $location = $request->filled('hash')
            ? Location::whereHash($request->hash)->first()
            : Location::firstOrCreate([
                'team_id' => Auth::user()->current_team_id,
                'facility_id' => $request->input('facility_id'),
                'area' => $request->input('area'),
                'enclosure' => $request->input('enclosure'),
            ]);

        $patientLocation = PatientLocation::create([
            'location_id' => $location->id,
            'patient_id' => $patient->id,
            'moved_in_at' => Timezone::convertFromLocalToUtc($request->input('moved_in_at')),
            'hours' => $request->hours,
            'comments' => $request->comments,
        ]);

        $patientLocation->patient_id = $patient->id;

        return back();
    }

    /**
     * Update a location.
     */
    public function update(Request $request, Patient $patient, PatientLocation $patientLocation)
    {
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        $request->validate([
            'moved_in_at' => [
                'required',
                'date',
                'after_or_equal:'.$admittedAt,
            ],
            'facility_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::PATIENT_LOCATION_FACILITIES),
            ],
            'area' => 'required',
        ]);

        $patientLocation->validateOwnership(Auth::user()->current_team_id);

        if ($this->physicalLocationChanged($request, $patientLocation)) {
            $location = Location::firstOrCreate([
                'team_id' => Auth::user()->current_team_id,
                'facility_id' => $request->input('facility_id'),
                'area' => $request->input('area'),
                'enclosure' => $request->input('enclosure'),
            ]);

            $patientLocation->update([
                'location_id' => $location->id,
                'moved_in_at' => Timezone::convertFromLocalToUtc($request->input('moved_in_at')),
                'comments' => $request->input('comments'),
                'hours' => $request->input('hours'),
            ]);
        } else {
            $patientLocation->update([
                'moved_in_at' => Timezone::convertFromLocalToUtc($request->input('moved_in_at')),
                'comments' => $request->input('comments'),
                'hours' => $request->input('hours'),
            ]);
        }

        return back();
    }

    /**
     * Delete a location in storage.
     */
    public function destroy(Patient $patient, PatientLocation $patientLocation)
    {
        $patientLocation->validateOwnership(Auth::user()->current_team_id)->delete();

        return back();
    }

    /**
     * Determine if the physical location of the request differs from the patient location.
     *
     * @param  Request $request
     * @param  PatientLocation $patientLocation
     * @return bool
     */
    private function physicalLocationChanged(Request $request, PatientLocation $patientLocation): bool
    {
        $diff = array_diff_assoc(
            Arr::sortRecursive($request->only('facility_id', 'area', 'enclosure')),
            Arr::sortRecursive($patientLocation->location->only('facility_id', 'area', 'enclosure'))
        );

        return count($diff) > 0;
    }
}
