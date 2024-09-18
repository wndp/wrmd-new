<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Models\Patient;
use App\Models\PatientLocation;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PatientLocationsController extends Controller
{
    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings('timezone'))->startOfDay();

        $request->validate([
            'moved_in_at' => [
                'required',
                'date',
                'after_or_equal:'.$admittedAt
            ],
            'facility_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::PATIENT_LOCATION_FACILITIES),
            ],
            'area' => 'required_without:hash',
            'hash' => [
                Rule::exists('locations', 'hash')->where(function ($query) {
                    $query->where('account_id', Auth::user()->current_team_id);
                }),
            ],
        ], [
            'moved_in_at.required' => 'The moved in at date field is required.',
            'moved_in_at.date' => 'The moved in at date is not a valid date.',
            'area.required_without' => 'The area field is required.',
            'hash.exists' => 'The provided hash does not exist.',
        ]);

        $patientLocation = new PatientLocation([
            'moved_in_at' => Timezone::convertFromLocalToUtc($request->input('moved_in_at')),
            'facility_id' => $request->facility_id,
            'hours' => $request->hours,
            'area' => $request->area,
            'enclosure' => $request->enclosure,
            'comments' => $request->comments,
        ]);

        $patientLocation->patient_id = $patient->id;

        if ($request->has('hash')) {
            $location = Location::whereHash($request->hash)->first();

            $patientLocation->location_id = $location->id;
            $patientLocation->area = $location->area;
            $patientLocation->enclosure = $location->enclosure;
        } elseif ($request->enclosure) {
            // $location = Location::firstOrNew([
            //     'account_id' => Auth::user()->current_team_id,
            //     'area' => $request->area,
            //     'enclosure' => $request->enclosure,
            // ]);

            // $location->account_id = Auth::user()->current_team_id;
            // $location->save();

            // $patientLocation->location_id = $location->id;
        }

        $patientLocation->save();

        return back();
    }

    /**
     * Update a location.
     */
    public function update(Request $request, Patient $patient, PatientLocation $location)
    {
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings('timezone'))->startOfDay();

        $request->validate([
            'moved_in_at' => [
                'required',
                'date',
                'after_or_equal:'.$admittedAt
            ],
            'facility_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::PATIENT_LOCATION_FACILITIES),
            ],
            'area' => 'required',
        ], [
            'moved_in_at.required' => 'The moved in at date field is required.',
            'moved_in_at.date' => 'The moved in at date is not a valid date.',
        ]);

        $location->validateOwnership(Auth::user()->current_team_id)
            ->update([
                'moved_in_at' => Timezone::convertFromLocalToUtc($request->input('moved_in_at')),
                'facility_id' => $request->input('facility_id'),
                'area' => $request->input('area'),
                'enclosure' => $request->input('enclosure'),
                'comments' => $request->input('comments'),
                'hours' => $request->input('hours'),
            ]);

        return back();
    }

    /**
     * Delete a location in storage.
     */
    public function destroy(Patient $patient, PatientLocation $location)
    {
        $location->validateOwnership(Auth::user()->current_team_id)->delete();

        return back();
    }
}
