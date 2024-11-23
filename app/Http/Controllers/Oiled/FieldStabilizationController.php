<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\ExamBodyPart;
use App\Events\ExamUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExamRequest;
use App\Models\AttributeOption;
use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\OptionsStore;
use Inertia\Inertia;

class FieldStabilizationController extends Controller
{
    public function edit()
    {
        $admission = $this->loadAdmissionAndSharePagination();

        $patientTaxaClassAgeUnits = match ($admission->patient->taxon?->class) {
            'Mammalia' => AttributeOptionName::EXAM_MAMMALIA_AGE_UNITS->value,
            'Amphibia' => AttributeOptionName::EXAM_AMPHIBIA_AGE_UNITS->value,
            'Reptilia' => AttributeOptionName::EXAM_REPTILIA_AGE_UNITS->value,
            'Aves' => AttributeOptionName::EXAM_AVES_AGE_UNITS->value,
            default => null,
        };

        OptionsStore::add([
            new LocaleOptions(),
            'bodyPartOptions' => Options::enumsToSelectable(ExamBodyPart::cases()),
            'taxaClassAgeUnits' => [],
            // Options::arrayToSelectable(AttributeOption::getDropdownOptions([
            //     $patientTaxaClassAgeUnits
            // ])),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::EXAM_TYPES->value,
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

        [
            $fieldExamTypeId,
            $abnormalBodyPartFindingID
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::EXAM_TYPES->value, AttributeOptionUiBehavior::EXAM_TYPE_IS_FIELD->value],
            [AttributeOptionName::EXAM_BODY_PART_FINDINGS->value, AttributeOptionUiBehavior::EXAM_BODY_PART_FINDING_IS_ABNORMAL->value],
        ]);

        $admission->patient->load('oilProcessing');
        $exam = $admission->patient->field_exam;

        return Inertia::render('Oiled/FieldStabilization', [
            'attributeOptionUiBehaviors' => \App\Models\AttributeOptionUiBehavior::getGroupedAttributeOptions([
                AttributeOptionName::EXAM_TYPES->value,
            ]),
            'patient' => $admission->patient,
            'exam' => $admission->patient
                ->exams()
                ->where('exam_type_id', $fieldExamTypeId)
                ->first(),
            'abnormalBodyPartFindingID' => $abnormalBodyPartFindingID
        ]);
    }

    public function update(ExamRequest $request, Patient $patient)
    {
        $exam = tap(
            Exam::getFieldExam($patient->id),
            fn ($exam) => $exam->fill($request->dataFromRequest())->save()
        );

        event(new ExamUpdated($exam));

        return back();
    }
}
