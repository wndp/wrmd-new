<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveOilWaterproofingAssessmentRequest extends FormRequest
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
            'evaluated_at' => 'required|date',
            'examiner' => 'required|string',
            'buoyancy_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_CONDITIONING_BUOYANCIES),
            ],
            'hauled_out_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_CONDITIONING_HAULED_OUTS),
            ],
            'preening_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_CONDITIONING_PREENINGS),
            ],
            'self_feeding_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_CONDITIONING_UNKNOWN_BOOL),
            ],
            'flighted_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_CONDITIONING_UNKNOWN_BOOL),
            ],
            'areas_wet_to_skin' => 'nullable|array',
            'areas_wet_to_skin.*' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::OILED_CONDITIONING_AREAS_WET_TO_SKIN),
            ],
            'comments' => 'nullable|string',
        ];
    }
}
