<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PrescriptionRequest extends FormRequest
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
            'drug' => 'required|string',
            'rx_started_at' => 'required|date',
            'rx_ended_at' => 'nullable|date|after_or_equal:rx_started_at',
            'concentration' => 'nullable|numeric',
            'concentration_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS),
            ],
            'dosage' => 'nullable|numeric',
            'dosage_unit' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_DOSAGE_UNITS),
            ],
            'loading_dose' => 'nullable|numeric',
            'loading_dose_unit' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_DOSE_UNITS),
            ],
            'dose' => 'nullable|numeric',
            'dose_unit' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_DOSE_UNITS),
            ],
            'frequency_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_FREQUENCIES),
            ],
            'route' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_ROUTES),
            ],
            'is_controlled_substance' => 'nullable|boolean',
            'instructions' => 'nullable|string',
            'veterinarian_id' => [
                'nullable',
                'string',
                Rule::exists('veterinarians', 'id')->where('team_id', $this->user()->current_team_id),
            ],
        ];
    }

    public function messages()
    {
        return [
            'rx_started_at.required' => 'The start date field is required.',
            'rx_started_at.date' => 'The start date is not a valid date.',
            'rx_ended_at.date' => 'The end date is not a valid date.',
            'rx_ended_at.after_or_equal' => 'The end date must be a date after or equal to the start date.',
        ];
    }
}
