<?php

namespace App\Http\Controllers;

use App\Actions\AdmitPatient;
use App\Models\Patient;
use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\OptionsStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DuplicationController extends Controller
{
    /**
     * Show the form for duplicating an existing patient.
     */
    public function create(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        OptionsStore::add([
            new LocaleOptions,
            'availableYears' => Options::arrayToSelectable(
                AdmitPatient::availableYears(Auth::user()->currentTeam)->toArray()
            ),
        ]);

        return Inertia::render('Patients/Duplicate', [
            'patient' => $admission->patient,
        ]);
    }

    /**
     * Replicate a previously created admission in storage.
     */
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $request->validate([
            'admitted_at' => 'required|date',
            'case_year' => 'required|in:'.AdmitPatient::availableYears(Auth::user()->currentTeam)->implode(','),
            'admitted_by' => 'required',
            'address_found' => 'required',
            'city_found' => 'required',
            'subdivision_found' => 'required',
            'found_at' => 'required|date',
            'reason_for_admission' => 'required',
        ], [
            'admitted_at.required' => __('The date admitted field is required.'),
            'admitted_at.date' => __('The date admitted is not a valid date.'),
            'found_at.required' => __('The date found field is required.'),
            'found_at.date' => __('The date found is not a valid date.'),
        ]);

        $patient->validateOwnership(Auth::user()->current_team_id);

        $admissions = AdmitPatient::run(
            Auth::user()->currentTeam,
            $request->input('case_year'),
            $patient->rescuer,
            array_merge($request->only([
                'reference_number',
                'microchip_number',
                'admitted_at',
                'admitted_by',
                'transported_by',
                'found_at',
                'address_found',
                'city_found',
                'county_found',
                'subdivision_found',
                'postal_code_found',
                'lat_found',
                'lng_found',
                'reason_for_admission',
                'care_by_rescuer',
                'notes_about_rescue',
            ]), [
                'common_name' => $patient->common_name,
            ]),
            $request->input('duplicates_to_make', 1)
        );

        $firstAdmission = $admissions->first();

        $message = $admissions->count() > 1
            ? __('Patients :firstCaseNumber through :lastCaseNumber created.', ['firstCaseNumber' => $firstAdmission->case_number, 'lastCaseNumber' => $admissions->last()->case_number])
            : __('Patient :caseNumber created.', ['caseNumber' => $firstAdmission->case_number]);

        return redirect()->route('patients.initial.edit', [
            'y' => $admissions->first()->case_year,
            'c' => $admissions->first()->case_id,
        ], 303)
            ->with('notification.heading', 'Success!')
            ->with('notification.text', $message);
    }
}
