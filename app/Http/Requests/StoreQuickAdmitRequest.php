<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use App\Actions\AdmitPatient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Support\Wrmd;
use Carbon\Carbon;

class StoreQuickAdmitRequest extends FormRequest
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
            // 'reference_number' => 'nullable|string',
            // 'microchip_number' => 'nullable|string',
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
