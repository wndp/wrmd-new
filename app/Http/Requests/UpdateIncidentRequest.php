<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentRequest extends FormRequest
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
        $incident = $this->route('incident')->validateOwnership($this->user()->current_team_id);
        $resolvedAt = $incident->resolved_at?->timezone(Wrmd::settings('timezone'));
        $now = Carbon::now(Wrmd::settings('timezone'));

        return [
            'reported_at' => 'required|date|before_or_equal:'.($resolvedAt ?? $now),
            'occurred_at' => 'required|date|before_or_equal:'.($resolvedAt ?? $now),
            'duration_of_call' => 'nullable|numeric',
            'recorded_by' => 'required|string',
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
            'suspected_species' => 'required_with:number_of_animals'
        ];
    }

    public function messages()
    {
        return [
            'reported_at.required' => __('The date reported field is required.'),
            'reported_at.date' => __('The date reported field is not a valid date.'),
            'occurred_at.required' => __('The date occurred field is required.'),
            'occurred_at.date' => __('The date occurred field is not a valid date.'),
        ];
    }
}
