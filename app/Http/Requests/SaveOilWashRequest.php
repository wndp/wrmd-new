<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveOilWashRequest extends FormRequest
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
        return [
            'washed_at' => 'required|date',
            'pre_treatment_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_WASH_PRE_TREATMENTS),
            ],
            'pre_treatment_duration' => 'nullable|numeric',
            'wash_type_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_WASH_TYPES),
            ],
            'wash_duration' => 'nullable|numeric',
            'detergent_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_WASH_DETERGENTS),
            ],
            'rinse_duration' => 'nullable|numeric',
            'washer' => 'nullable|string',
            'handler' => 'nullable|string',
            'rinser' => 'nullable|string',
            'dryer' => 'nullable|string',
            'drying_method_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_WASH_DRYING_METHODS),
            ],
            'drying_duration' => 'nullable|numeric',
            'observations' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'washed_at.required' => 'The washed at date field is required.',
            'washed_at.date' => 'The washed at date is not a valid date.',
        ];
    }
}
