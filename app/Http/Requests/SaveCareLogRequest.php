<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Timezone;
use Illuminate\Foundation\Http\FormRequest;

class SaveCareLogRequest extends FormRequest
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
            'care_at' => [
                'required',
                'date',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'required_without:comments',
            ],
            'weight_unit_id' => [
                'nullable',
                'required_with:weight',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_WEIGHT_UNITS),
            ],
            'temperature' => [
                'nullable',
                'numeric',
            ],
            'temperature_unit_id' => [
                'nullable',
                'required_with:temperature',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::EXAM_TEMPERATURE_UNITS),
            ],
            'comments' => [
                'nullable',
                'required_without:weight',
            ],
        ];
    }

    public function dataFromRequest(): array
    {
        $careAt = Timezone::convertFromLocalToUtc($this->get('care_at'));

        return array_merge([
            'date_care_at' => $careAt->toDateString(),
            'time_care_at' => $careAt->toTimeString(),
        ], $this->only([
            'weight',
            'weight_unit_id',
            'temperature',
            'temperature_unit_id',
            'comments',
        ]));
    }
}
