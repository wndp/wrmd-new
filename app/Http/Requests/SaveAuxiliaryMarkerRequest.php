<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveAuxiliaryMarkerRequest extends FormRequest
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
            'auxiliary_marker' => 'required|string',
            'auxiliary_marker_color_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_AUXILLARY_COLOR_CODES),
            ],
            'auxiliary_side_of_bird_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_AUXILLARY_SIDE_OF_BIRD),
            ],
            'auxiliary_marker_type_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_AUXILLARY_MARKER_TYPE_CODES),
            ],
            'auxiliary_marker_code_color_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_AUXILLARY_CODE_COLOR),
            ],
            'auxiliary_placement_on_leg_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_PLACEMENT_ON_LEG),
            ],
        ];
    }
}
