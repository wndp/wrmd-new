<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Requests\StoreQuickAdmitRequest;
use App\Models\Exam;
use App\Support\Timezone;

class PatientsQuickAdmitController extends Controller
{
    public function create()
    {
        OptionsStore::add([
            new LocaleOptions,
            'actionsAfterStore' => Options::arrayToSelectable([
                'return' => __('I want to admit another patient'),
                'view' => __("I want to view this patient's record"),
                'batch' => __('I want to batch update all the admitted patients'),
            ]),
            'availableYears' => Options::arrayToSelectable(
                AdmitPatient::availableYears(Auth::user()->currentTeam)->toArray()
            ),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::TAXA_MORPHS->value,
                AttributeOptionName::EXAM_SEXES->value,
                AttributeOptionName::EXAM_WEIGHT_UNITS->value,
                AttributeOptionName::EXAM_TEMPERATURE_UNITS->value,
                AttributeOptionName::EXAM_ATTITUDES->value,
                AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS->value,
                AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS->value,
                AttributeOptionName::EXAM_REPTILIA_AGE_UNITS->value,
                AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS->value,
                AttributeOptionName::EXAM_AVES_AGE_UNITS->value,
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionName::PATIENT_RELEASE_TYPES->value,
                AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
            ]),
        ]);

        $this->shareLastCaseId();

        return Inertia::render('Patients/QuickAdmit');
    }

    public function store(StoreQuickAdmitRequest $request)
    {
        $admissions = AdmitPatient::run(
            Auth::user()->currentTeam,
            $request->input('case_year'),
            [],
            $request->all(),
            $request->get('cases_to_create', 1)
        )->each(function ($admission) use ($request) {
            $admission->patient->update([
                'disposition' => $request->disposition,
            ]);

            [$intakeExamTypeId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
                AttributeOptionName::EXAM_TYPES->value,
                AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE->value,
            ]);

            $admittedAt = Timezone::convertFromLocalToUtc($request->input('admitted_at'));

            Exam::updateOrCreate(
                ['patient_id' => $admission->patient_id, 'exam_type_id' => $intakeExamTypeId],
                [
                    'date_examined_at' => $admittedAt->toDateString(),
                    'time_examined_at' => $admittedAt->toTimeString(),
                    ...$request->only([
                        'sex_id',
                        'weight',
                        'weight_unit_id',
                        'age',
                        'age_unit_id',
                        'attitude_id',
                    ]),
                ]
            );
        });

        $firstAdmission = $admissions->first();
        $message = $admissions->count() > 1
            ? __('Patients :firstCaseNumber through :lastCaseNumber created.', ['firstCaseNumber' => $firstAdmission->case_number, 'lastCaseNumber' => $admissions->last()->case_number])
            : __('Patient :caseNumber created.', ['caseNumber' => $firstAdmission->case_number]);

        return redirect()->route('patients.quick_admit.create')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', $message);
    }
}
