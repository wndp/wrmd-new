<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientsQuickAdmitController extends Controller
{
    public function create(
        TaxonomyOptions $taxonomyOptions,
        LocaleOptions $localeOptions,
        ExamOptions $examOptions
    ) {
        OptionsStore::merge($taxonomyOptions);
        OptionsStore::merge($localeOptions);
        OptionsStore::merge($examOptions);
        OptionsStore::merge([
            'availableYears' => Options::arrayToSelectable(
                AdmitPatient::availableYears(Auth::user()->currentAccount)->toArray()
            ),
        ]);

        $this->shareLastCaseId();

        return Inertia::render('Patients/QuickAdmit');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'case_year' => 'required|in:'.AdmitPatient::availableYears(Auth::user()->currentAccount)->implode(','),
            'common_name' => 'required',
            'admitted_at' => 'required|date',
            'admitted_by' => 'required',
            'address_found' => 'required',
            'city_found' => 'required',
            'subdivision_found' => 'required',
            'found_at' => 'required|date',
            'reasons_for_admission' => 'required',
        ], [
            'admitted_at.required' => __('The date admitted field is required.'),
            'admitted_at.date' => __('The date admitted is not a valid date.'),
            'found_at.required' => __('The date found field is required.'),
            'found_at.date' => __('The date found is not a valid date.'),
        ]);

        $admissions = AdmitPatient::run(
            Auth::user()->currentAccount,
            $request->case_year,
            [],
            $request->all(),
            $request->get('cases_to_create', 1)
        )->each(function ($admission) use ($request) {
            $admission->patientPromise()->update([
                'disposition' => $request->disposition,
            ]);

            Exam::getIntakeExam($admission->patient_id)->fill(array_merge([
                'examiner' => request('admitted_by'),
                'examined_at' => $request->convertDateFromLocal('admitted_at'),
            ], $request->only([
                'sex',
                'weight',
                'weight_unit',
                'age',
                'age_unit',
                'attitude',
            ])))->save();
        });

        // $patientData = array_merge($request->all([
        //     'admitted_at',
        //     'admitted_by',
        //     'found_at',
        //     'microchip_number',
        //     'reference_number',
        //     'reasons_for_admission',
        //     'care_by_rescuer',
        //     'notes_about_rescue',
        //     'disposition_location',
        //     'disposition_subdivision',
        //     'transfer_type',
        //     'release_type',
        //     'reason_for_disposition',
        //     'dispositioned_by',
        // ]), [
        //     'common_name' => $request->commonName,
        //     'address_found' => $request->has('dispositionToLocation') ? $request->disposition_location : null,
        //     'subdivision_found' => $request->has('dispositionToLocation') ? $request->disposition_subdivision : null,
        //     'dispositioned_at' => format_date($request->dispositioned_at, 'Y-m-d'),
        //     'criminal_activity' => $request->input('criminal_activity', false),
        //     'carcass_saved' => $request->input('carcass_saved', false),
        // ]);

        // $admissions = AdmitPatient::casesToCreate(request('cases_to_create', 1))->process(
        //     auth()->user()->currentAccount,
        //     $request->case_year,
        //     [],
        //     $patientData
        // )
        //     ->each(function ($admission) use ($request) {
        //         $admission->patientPromise()->update($request->all('disposition'));

        //         if (is_array($request->exams) && array_filter($request->exams)) {
        //             Exam::getIntakeExam($admission->patient_id)->fill(array_merge($request->exams, [
        //                 'examined_at' => $request->admitted_at,
        //                 'examiner' => request('admitted_by'),
        //             ]))->save();
        //         }
        //     });

        $firstAdmission = $admissions->first();
        $message = $admissions->count() > 1
            ? __('Patients :firstCaseNumber through :lastCaseNumber created.', ['firstCaseNumber' => $firstAdmission->case_number, 'lastCaseNumber' => $admissions->last()->case_number])
            : __('Patient :caseNumber created.', ['caseNumber' => $firstAdmission->case_number]);

        return redirect()->route('patients.quick_admit.create')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', $message);

        // $message = $admissions->count() > 1
        //     ? "Records #{$admissions->first()->case_number} through #{$admissions->last()->case_number} created."
        //     : "Record #{$admissions->first()->case_number} created.";

        // flash()->success($message);

        //return redirect()->route('quickadmit.create');
    }
}
