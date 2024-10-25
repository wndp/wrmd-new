<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\ExamBodyPart;
use App\Enums\SettingKey;
use App\Models\AttributeOption;
use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\OptionsStore;
use App\Support\Wrmd;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class InitialCareController extends Controller
{
    /**
     * Show the form for updating the initial care.
     */
    public function __invoke(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        if (! ($teamIsInPossession = $admission->patient->team_possession_id === Auth::user()->current_team_id)) {
            $admission->load('patient.possession');
        }

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

        if (Wrmd::settings(SettingKey::SHOW_TAGS)) {
            OptionsStore::add([
                'circumstancesOfAdmission' => Options::arrayToSelectable(app(CircumstancesOfAdmission::class)::terms()),
                'clinicalClassifications' => Options::arrayToSelectable(app(ClinicalClassifications::class)::terms()),
                'categorizationOfClinicalSigns' => Options::arrayToSelectable(app(CategorizationOfClinicalSigns::class)::terms()),
            ]);
        }

        [
            $intakeExamTypeId,
            $abnormalBodyPartFindingID
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::EXAM_TYPES->value, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE->value],
            [AttributeOptionName::EXAM_BODY_PART_FINDINGS->value, AttributeOptionUiBehavior::EXAM_BODY_PART_FINDING_IS_ABNORMAL->value],
        ]);

        return Inertia::render('Patients/Initial', [
            'attributeOptionUiBehaviors' => \App\Models\AttributeOptionUiBehavior::getGroupedAttributeOptions([
                AttributeOptionName::EXAM_TYPES->value,
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
            ]),
            'teamIsInPossession' => $teamIsInPossession,
            'patient' => $admission->patient,
            'exam' => $admission->patient
                ->exams()
                ->where('exam_type_id', $intakeExamTypeId)
                ->first(),
            'abnormalBodyPartFindingID' => $abnormalBodyPartFindingID
        ]);
    }
}
