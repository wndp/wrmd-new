<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class LabChemistryResultRequest extends FormRequest
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
            'analysis_date_at' => 'required|date',
            'technician' => 'nullable|string',
            'accession_number' => 'nullable|string',
            'analysis_facility' => 'nullable|string',
            'comments' => 'nullable|string',
            'ast' => 'nullable|numeric',
            'ck' => 'nullable|numeric',
            'ggt' => 'nullable|numeric',
            'amy' => 'nullable|numeric',
            'alb' => 'nullable|numeric',
            'alp' => 'nullable|numeric',
            'alt' => 'nullable|numeric',
            'tp' => 'nullable|numeric',
            'glob' => 'nullable|numeric',
            'bun' => 'nullable|numeric',
            'chol' => 'nullable|numeric',
            'crea' => 'nullable|numeric',
            'ba' => 'nullable|numeric',
            'ca' => 'nullable|numeric',
            'ca_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_CHEMISTRY_UNITS),
            ],
            'p' => 'nullable|numeric',
            'p_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_CHEMISTRY_UNITS),
            ],
            'cl' => 'nullable|numeric',
            'cl_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_CHEMISTRY_UNITS),
            ],
            'k' => 'nullable|numeric',
            'k_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_CHEMISTRY_UNITS),
            ],
            'na' => 'nullable|numeric',
            'na_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_CHEMISTRY_UNITS),
            ],
            'total_bilirubin' => 'nullable|numeric',
            'ag_ratio' => 'nullable|numeric',
            'tri' => 'nullable|numeric',
            'nak_ratio' => 'nullable|numeric',
            'cap_ratio' => 'nullable|numeric',
            'glu' => 'nullable|numeric',
            'ua' => 'nullable|numeric',
        ];
    }
}
