<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class LabCbcResultRequest extends FormRequest
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

            'packed_cell_volume' => 'nullable|numeric',
            'total_solids' => 'nullable|numeric',
            'buffy_coat' => 'nullable|numeric',
            'plasma_color' => 'nullable|numeric',
            'white_blod_cell_count' => 'nullable|numeric',
            'white_blod_cell_count_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'segmented_neutrophils' => 'nullable|numeric',
            'segmented_neutrophils_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'band_neutrophils' => 'nullable|numeric',
            'band_neutrophils_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'eosinophils' => 'nullable|numeric',
            'eosinophils_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'basophils' => 'nullable|numeric',
            'basophils_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'lymphocytes' => 'nullable|numeric',
            'lymphocytes_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'monocytes' => 'nullable|numeric',
            'monocytes_unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::LAB_RESULT_QUANTITY_UNITS),
            ],
            'hemoglobin' => 'nullable|numeric',
            'mean_corpuscular_volume' => 'nullable|numeric',
            'mean_corpuscular_hemoglobin' => 'nullable|numeric',
            'mean_corpuscular_hemoglobin_concentration' => 'nullable|numeric',
            'erythrocytes' => 'nullable|string',
            'reticulocytes' => 'nullable|string',
            'thrombocytes' => 'nullable|string',
            'polychromasia' => 'nullable|string',
            'anisocytosis' => 'nullable|string',
        ];
    }
}
