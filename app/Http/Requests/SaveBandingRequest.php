<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use Illuminate\Foundation\Http\FormRequest;

class SaveBandingRequest extends FormRequest
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
            'band_number' => 'required|string',
            'banded_at' => 'required|date|after_or_equal:'.$admittedAt,
            'age_code_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_AGE_CODES),
            ],
            'how_aged_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_HOW_AGED_CODES),
            ],
            'sex_code_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_SEX_CODES),
            ],
            'how_sexed_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_HOW_SEXED_CODES),
            ],
            'status_code_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_STATUS_CODES),
            ],
            'additional_status_code_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_ADDITIONAL_INFORMATION_STATUS_CODES),
            ],
            'band_size_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_BAND_SIZES),
            ],
            'band_disposition_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::BANDING_DISPOSITION_CODES),
            ],
            'master_bander_number' => 'nullable|string',
            'banded_by' => 'nullable|string',
            'location_number' => 'nullable|string',
            'remarks' => 'nullable|string',
        ];
    }

    public function messages()
    {
        $patient = $this->route('patient');
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        return [
            'banded_at.required' => 'The banding date field is required.',
            'banded_at.date' => 'The banding date is not a valid date.',
            'banded_at.after_or_equal' => 'The banding date must be a date after or equal to '.$admittedAt,
        ];
    }
}
