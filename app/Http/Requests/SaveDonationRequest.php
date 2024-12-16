<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveDonationRequest extends FormRequest
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
            'donated_at' => 'required|date',
            'value' => 'required|numeric',
            'method_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::DONATION_METHODS),
            ],
            'comments' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'donated_at.required' => __('The donation date field is required.'),
            'donated_at.date' => __('The donation date is not a valid date.'),
        ];
    }
}
