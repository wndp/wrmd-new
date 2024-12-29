<?php

namespace App\Http\Requests;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Timezone;
use App\Support\Wrmd;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Ability::MANAGE_EXAMS->value);
    }

    public function rules(): array
    {
        $patient = $this->route('patient')->validateOwnership($this->user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        [$examTypeCanOnlyOccurOnceIds] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::EXAM_TYPES->value,
            AttributeOptionUiBehavior::EXAM_TYPE_CAN_ONLY_OCCUR_ONCE->value,
        ]);

        return [
            'exam_type_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_TYPES),
                Rule::when(
                    in_array($this->input('exam_type_id'), $examTypeCanOnlyOccurOnceIds),
                    Rule::unique('exams')
                        ->where('patient_id', $patient->id)
                        ->where('exam_type_id', $this->input('exam_type_id'))
                        ->ignore($this->route('exam') ?? 'NULL')
                ),
            ],
            'examined_at' => 'required|date|after_or_equal:'.$admittedAt,
            'examiner' => [
                'required',
                'string',
            ],
            'sex_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_SEXES),
            ],
            'weight' => [
                'nullable',
                'numeric',
            ],
            'weight_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_WEIGHT_UNITS),
            ],
            'body_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_CONDITIONS),
            ],
            'age' => [
                'nullable',
                'numeric',
            ],
            'age_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_AVES_AGE_UNITS),
            ],
            'attitude_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_ATTITUDES),
            ],
            'dehydration_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_DEHYDRATIONS),
            ],
            'temperature' => [
                'nullable',
                'numeric',
            ],
            'temperature_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_TEMPERATURE_UNITS),
            ],
            'mucous_membrane_color_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_MUCUS_MEMBRANE_COLORS),
            ],
            'mucous_membrane_texture_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_MUCUS_MEMBRANE_TEXTURES),
            ],
            'head' => [
                'nullable',
                'string',
            ],
            'cns' => [
                'nullable',
                'string',
            ],
            'cardiopulmonary' => [
                'nullable',
                'string',
            ],
            'gastrointestinal' => [
                'nullable',
                'string',
            ],
            'musculoskeletal' => [
                'nullable',
                'string',
            ],
            'integument' => [
                'nullable',
                'string',
            ],
            'body' => [
                'nullable',
                'string',
            ],
            'forelimb' => [
                'nullable',
                'string',
            ],
            'hindlimb' => [
                'nullable',
                'string',
            ],
            'head_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'cns_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'cardiopulmonary_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'gastrointestinal_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'musculoskeletal_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'integument_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'body_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'forelimb_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'hindlimb_finding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_BODY_PART_FINDINGS),
            ],
            'treatment' => [
                'nullable',
                'string',
            ],
            'nutrition' => [
                'nullable',
                'string',
            ],
            'comments' => [
                'nullable',
                'string',
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'examined_at.required' => 'The examined at date field is required.',
            'examined_at.date' => 'The examined at date is not a valid date.',
        ];
    }

    public function dataFromRequest(): array
    {
        $examinedAt = Timezone::convertFromLocalToUtc($this->get('examined_at'));

        return array_merge([
            'date_examined_at' => $examinedAt->toDateString(),
            'time_examined_at' => $examinedAt->toTimeString(),
        ], $this->only([
            'exam_type_id',
            'sex_id',
            'weight',
            'weight_unit_id',
            'body_condition_id',
            'age',
            'age_unit_id',
            'attitude_id',
            'dehydration_id',
            'temperature',
            'temperature_unit_id',
            'mucous_membrane_color_id',
            'mucous_membrane_texture_id',
            'head',
            'cns',
            'cardiopulmonary',
            'gastrointestinal',
            'musculoskeletal',
            'integument',
            'body',
            'forelimb',
            'hindlimb',
            'head_finding_id',
            'cns_finding_id',
            'cardiopulmonary_finding_id',
            'gastrointestinal_finding_id',
            'musculoskeletal_finding_id',
            'integument_finding_id',
            'body_finding_id',
            'forelimb_finding_id',
            'hindlimb_finding_id',
            'treatment',
            'nutrition',
            'comments',
            'examiner',
        ]));
    }
}
