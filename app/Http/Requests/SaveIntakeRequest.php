<?php

namespace App\Http\Requests;

use App\Enums\SettingKey;
use App\Support\Wrmd;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveIntakeRequest extends FormRequest
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
            'transported_by' => 'nullable',
            'admitted_by' => 'required',
            'found_at' => 'required|date',
            'address_found' => 'required',
            'city_found' => 'required',
            'county_found' => 'nullable',
            'subdivision_found' => 'required',
            'latitude_found' => Rule::when(
                Wrmd::settings(SettingKey::SHOW_GEOLOCATION_FIELDS),
                'nullable|required_with:longitude_found|numeric|between:-90,90'
            ),
            'longitude_found' => Rule::when(
                Wrmd::settings(SettingKey::SHOW_GEOLOCATION_FIELDS),
                'nullable|required_with:latitude_found|numeric|between:-180,180'
            ),
            'reason_for_admission' => 'required',
            'care_by_rescuer' => 'nullable',
            'notes_about_rescue' => 'nullable',
        ];
    }
}
