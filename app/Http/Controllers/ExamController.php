<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\ExamBodyPart;
use App\Http\Requests\ExamRequest;
use App\Models\Admission;
use App\Models\AttributeOption;
use App\Models\Exam;
use App\Models\Patient;
use App\Options\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $patient = $this->loadAdmissionAndSharePagination();

        return Inertia::render('Patients/Exams/Index', [
            'patient' => $patient,
            'exams' => $patient->exams
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admission = $this->loadAdmissionAndSharePagination();

        $this->transformAndShareOptions($admission->patient, $admission->patient->exams);

        return Inertia::render('Patients/Exams/Create', [
            'patient' => $admission->patient,
        ]);
    }

    /**
     * Save an exam in storage.
     */
    public function store(ExamRequest $request, Patient $patient)
    {
        Exam::updateOrCreate(
            ['patient_id' => $patient->id, 'exam_type_id' => $request->exam_type_id],
            $request->dataFromRequest()
        );

        $admission = Admission::custody(Auth::user()->currentTeam, $patient);

        return redirect()->route('patients.exam.index', [
            'y' => $admission->case_year,
            'c' => $admission->case_id,
        ], 303);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     */
    public function edit(Patient $patient, Exam $exam)
    {
        $exam->validateOwnership(Auth::user()->current_team_id);

        $admission = $this->loadAdmissionAndSharePagination();

        $this->transformAndShareOptions($admission->patient, $admission->patient->exams, $exam);

        return Inertia::render('Patients/Exams/Edit', [
            'patient' => $admission->patient,
            'exams' => $admission->patient->exams
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExamRequest $request, Patient $patient, Exam $exam)
    {
        $exam->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWith($patient);

        $exam->update($request->dataFromRequest());

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient, Exam $exam)
    {
        $exam->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWith($patient)
            ->delete();

        $admission = Admission::custody(Auth::user()->currentTeam, $patient);

        return redirect()
            ->route('patients.exam.index', [
                'y' => $admission->case_year,
                'c' => $admission->case_id,
            ], 303)
            ->with('notification.heading', __('Exam Deleted'))
            ->with('notification.text', __(':examType exam on :examDate was deleted.', [
                'examType' => $exam->type,
                'examType' => $exam->examined_at_for_humans
            ]));
    }

    public function transformAndShareOptions(Patient $patient, Collection $exams, Exam $exam = null)
    {
        $patientTaxaClassAgeUnits = match ($patient->taxon?->class) {
            'Mammalia' => AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS->value,
            'Amphibia' => AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS->value,
            'Reptilia' => AttributeOptionName::EXAM_REPTILIA_AGE_UNITS->value,
            'Aves' => AttributeOptionName::EXAM_AVES_AGE_UNITS->value,
            default => null,
        };

        [
            $intakeExamTypeId,
            $releaseExamTypeId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::EXAM_TYPES->value, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE->value],
            [AttributeOptionName::EXAM_TYPES->value, AttributeOptionUiBehavior::EXAM_TYPE_IS_RELEASE->value],
        ]);

        $availableExamTypes = AttributeOption::where('name', AttributeOptionName::EXAM_TYPES->value)
            ->when(
                $exams->contains('exam_type_id', $intakeExamTypeId) && ($exam?->exam_type_id !== $intakeExamTypeId),
                fn ($q) => $q->where('exam_type_id', '!=', $intakeExamTypeId)
            )
            ->when(
                $exams->contains('exam_type_id', $releaseExamTypeId) && ($exam?->exam_type_id !== $releaseExamTypeId),
                fn ($q) => $q->where('exam_type_id', '!=', $releaseExamTypeId)
            )
            ->get()
            ->toArray();

        OptionsStore::add($availableExamTypes->optionsToSelectable());

        OptionsStore::add([
            'bodyPartOptions' => Options::enumsToSelectable(ExamBodyPart::cases()),
            'taxaClassAgeUnits' => Options::arrayToSelectable(AttributeOption::getDropdownOptions([
                $patientTaxaClassAgeUnits
            ])->first()),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::EXAM_WEIGHT_UNITS->value,
                AttributeOptionName::EXAM_DEHYDRATIONS->value,
                AttributeOptionName::EXAM_CHRONOLOGICAL_AGE_UNITS->value,
                AttributeOptionName::EXAM_ATTITUDES->value,
                AttributeOptionName::EXAM_SEXES->value,
                AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS->value,
                AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES->value,
                AttributeOptionName::EXAM_BODY_CONDITIONS->value,
                AttributeOptionName::EXAM_TEMPERATURE_UNITS->value,
                AttributeOptionName::EXAM_BODY_PART_FINDINGS->value,
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionName::PATIENT_RELEASE_TYPES->value,
                AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
            ])
        ]);
    }
}
