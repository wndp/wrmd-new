<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use Illuminate\Foundation\Http\FormRequest;

class SaveBandingMorphometricsRequest extends FormRequest
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
        $patient = $this->route('patient')->validateOwnership($this->user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        return [
            'measured_at' => 'required|date|after_or_equal:'.$admittedAt,
            'bill_length' => 'nullable|numeric',
            'bill_width' => 'nullable|numeric',
            'bill_depth' => 'nullable|numeric',
            'head_bill_length' => 'nullable|numeric',
            'culmen' => 'nullable|numeric',
            'exposed_culmen' => 'nullable|numeric',
            'wing_chord' => 'nullable|numeric',
            'flat_wing' => 'nullable|numeric',
            'tarsus_length' => 'nullable|numeric',
            'middle_toe_length' => 'nullable|numeric',
            'toe_pad_length' => 'nullable|numeric',
            'hallux_length' => 'nullable|numeric',
            'tail_length' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'samples_collected' => 'nullable|array',
            'samples_collected.*' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_SAMPLES_COLLECTED),
            ],
            'remarks' => 'nullable|string',
        ];
    }

    public function messages()
    {
        $patient = $this->route('patient');
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        return [
            'measured_at.required' => 'The date measured field is required.',
            'measured_at.date' => 'The date measured is not a valid date.',
            'measured_at.after_or_equal' => 'The date measured must be a date after or equal to '.$admittedAt,
        ];
    }
}
