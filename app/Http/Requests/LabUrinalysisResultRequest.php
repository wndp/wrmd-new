<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class LabUrinalysisResultRequest extends FormRequest
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
            'collection_method_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_URINE_COLLECTION_METHODS),
            ],
            'sg' => 'nullable|numeric',
            'ph' => 'nullable|numeric',
            'pro' => 'nullable|numeric',
            'glu' => 'nullable|string',
            'ket' => 'nullable|string',
            'bili' => 'nullable|string',
            'ubg' => 'nullable|string',
            'nitrite' => 'nullable|string',
            'bun' => 'nullable|numeric',
            'leuc' => 'nullable|string',
            'blood' => 'nullable|string',
            'color' => 'nullable|string',
            'turbidity_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_URINE_TURBIDITIES),
            ],
            'odor_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_URINE_ODORS),
            ],
            'crystals' => 'nullable|string',
            'casts' => 'nullable|string',
            'cells' => 'nullable|string',
            'microorganisms' => 'nullable|string',
        ];
    }
}
