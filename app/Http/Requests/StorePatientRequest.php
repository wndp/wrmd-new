<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use App\Actions\AdmitPatient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Support\Wrmd;
use Carbon\Carbon;

class StorePatientRequest extends FormRequest
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
        $now = Carbon::now(Wrmd::settings('timezone'));

        return [
            'incident_id' => 'nullable|integer|exists:incidents,id',
            'case_year' => 'required|in:'.AdmitPatient::availableYears(Auth::user()->currentTeam)->implode(','),
            'admitted_at' => 'required|date|before_or_equal:'.$now,
            'common_name' => 'required|string',
            'taxon_id' => 'nullable|integer',
            'morph_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::TAXA_MORPHS),
            ],
            'cases_to_create' => 'required|integer',
            'reference_number' => 'nullable|string',
            'microchip_number' => 'nullable|string',
            'entity_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::PERSON_ENTITY_TYPES),
            ],
            'organization' => 'nullable|string',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'alternate_phone' => 'nullable|string',
            'email' => 'nullable|email',
            'subdivision' => 'nullable|string',
            'city' => 'nullable|string',
            'address' => 'nullable|string',
            'county' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'notes' => 'nullable|string',
            'no_solicitations' => 'required|boolean',
            'is_volunteer' => 'nullable|boolean',
            'is_member' => 'nullable|boolean',
            'admitted_by' => 'required|string',
            'transported_by' => 'nullable|string',
            'found_at' => 'required|date|before_or_equal:'.$now,
            'address_found' => 'required|string',
            'city_found' => 'required|string',
            'county_found' => 'nullable|string',
            'subdivision_found' => 'required',
            'lat_found' => 'nullable|numeric|between:-90,90',
            'lng_found' => 'nullable|numeric|between:-180,180',
            'reason_for_admission' => 'required|string',
            'care_by_rescuer' => 'nullable|string',
            'notes_about_rescue' => 'nullable|string',
            'donation_method_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DONATION_METHODS),
            ],
            "donation_value" => 'nullable|numerid',
            "donation_comments" => 'nullable|string',
            'action_after_store' => 'required',
            'custom_values' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'admitted_at.required' => __('The date admitted field is required.'),
            'admitted_at.date' => __('The date admitted is not a valid date.'),
            'found_at.required' => __('The date found field is required.'),
            'found_at.date' => __('The date found is not a valid date.'),
        ];
    }
}
