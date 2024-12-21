<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomFieldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        [
            $customFieldGroupIsPatientId,
            $customFieldTypesRequiresOptionsIds
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::CUSTOM_FIELD_GROUPS->value, AttributeOptionUiBehavior::CUSTOM_FIELD_GROUP_IS_PATIENT->value],
            [AttributeOptionName::CUSTOM_FIELD_TYPES->value, AttributeOptionUiBehavior::CUSTOM_FIELD_TYPES_REQUIRES_OPTIONS->value],
        ]);

        return [
            'label' => 'required',
            'group_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::CUSTOM_FIELD_GROUPS),
            ],
            'location_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::CUSTOM_FIELD_LOCATIONS),
            ],
            'panel_id' => [
                Rule::requiredIf(fn () => $this->input('group_id') === $customFieldGroupIsPatientId),
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::CUSTOM_FIELD_PATIENT_PANELS),
            ],
            'type_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::CUSTOM_FIELD_TYPES),
            ],
            'is_required' => ['required', 'boolean'],
            'options' => Rule::requiredIf(fn () => in_array($this->input('type_id'), $customFieldTypesRequiresOptionsIds)),
        ];
    }
}
