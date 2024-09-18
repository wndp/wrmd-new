<?php

namespace App\Http\Requests;

use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonRequest extends FormRequest
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
            'entity_id' => [
                'nullable',
                'integer',
                new AttributeOptionExistsRule(AttributeOptionName::PERSON_ENTITY_TYPES),
            ],
            'organization' => 'required_without:first_name',
            'first_name' => 'required_without:organization',
            'email' => 'nullable|email',
            'no_solicitations' => 'nullable|boolean',
            'is_volunteer' => 'nullable|boolean',
            'is_member' => 'nullable|boolean',
        ];
    }
}
