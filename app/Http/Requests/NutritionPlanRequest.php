<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class NutritionPlanRequest extends FormRequest
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
            'name' => 'nullable|string',
            'started_at' => 'required|date',
            'ended_at' => 'nullable|date|after_or_equal:started_at',
            'frequency' => 'nullable|numeric',
            'frequency_unit_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_NUTRITION_FREQUENCIES),
            ],
            'route_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_NUTRITION_ROUTES),
            ],
            'description' => 'nullable|string',
            'ingredients' => 'nullable|array',
            'ingredients.*.quantity' => 'required|numeric',
            'ingredients.*.unit_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_NUTRITION_INGREDIENT_UNITS),
            ],
            'ingredients.*.ingredient' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'started_at.required' => 'The start date field is required.',
            'started_at.date' => 'The start date is not a valid date.',
            'ended_at.date' => 'The end date is not a valid date.',
        ];
    }
}
