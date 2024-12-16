<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentRequest extends FormRequest
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
        $now = Carbon::now(Wrmd::settings(SettingKey::TIMEZONE));

        return [
            'reported_at' => 'required|date|before_or_equal:'.($this->input('resolved_at') ?? $now),
            'occurred_at' => 'required|date|before_or_equal:'.($this->input('resolved_at') ?? $now),
            'duration_of_call' => 'nullable|numeric',
            'recorded_by' => 'required',
            'category_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule([
                    AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES,
                    AttributeOptionName::HOTLINE_ADMINISTRATIVE_CATEGORIES,
                    AttributeOptionName::HOTLINE_OTHER_CATEGORIES,
                ]),
            ],
            'incident_status_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::HOTLINE_STATUSES),
            ],
            'is_priority' => 'required|boolean',
            'number_of_animals' => 'nullable|integer',
            'resolved_at' => 'nullable|date|after_or_equal:'.($this->input('occurred_at') ?? $now),
            'given_information' => 'nullable|boolean',
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
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'subdivision' => 'nullable|string',
            'county' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'notes' => 'nullable|string',
            'no_solicitations' => 'nullable|boolean',
            'is_volunteer' => 'nullable|boolean',
            'is_member' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'reported_at.required' => 'The date reported field is required.',
            'reported_at.date' => 'The date reported field is not a valid date.',
            'occurred_at.required' => 'The date occurred field is required.',
            'occurred_at.date' => 'The date occurred field is not a valid date.',
            'person.required' => 'A reporting party is required.',
            'person.array' => 'A reporting party is required.',
            'resolved_at.date' => 'The date resolved field is not a valid date.',
        ];
    }
}
