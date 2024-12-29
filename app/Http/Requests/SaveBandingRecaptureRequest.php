<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use Illuminate\Foundation\Http\FormRequest;

class SaveBandingRecaptureRequest extends FormRequest
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
            'recaptured_at' => 'required|date|after_or_equal:'.$admittedAt->format('Y-m-d'),
            'recapture_disposition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_RECAPTURE_DISPOSITION_CODES),
            ],
            'present_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_PRESENT_CONDITION_CODES),
            ],
            'how_present_condition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_HOW_PRESENT_CONDITION_CODES),
            ],
        ];
    }

    public function messages()
    {
        $patient = $this->route('patient');
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        return [
            'recaptured_at.required' => 'The recapture date field is required.',
            'recaptured_at.date' => 'The recapture date is not a valid date.',
            'recaptured_at.after_or_equal' => 'The recapture date must be a date after or equal to '.$admittedAt,
        ];
    }
}
