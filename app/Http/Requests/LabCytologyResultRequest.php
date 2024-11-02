<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class LabCytologyResultRequest extends FormRequest
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
            'source' => 'required|string',
        ];
    }
}
