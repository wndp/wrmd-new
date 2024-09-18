<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class RecheckRequest extends FormRequest
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
            'recheck_start_at' => 'required|date',
            'recheck_end_at' => 'nullable|date|after_or_equal:recheck_start_at',
            'frequency_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_FREQUENCIES),
            ],
            'assigned_to_id' => [
                'required',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DAILY_TASK_ASSIGNMENTS),
            ],
            'description' => 'required',
        ];
    }
}
