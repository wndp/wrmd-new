<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubAccountRequest extends FormRequest
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
            'name' => 'required',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'subdivision' => 'required',
            'postal_code' => 'nullable',
            'contact_name' => 'required',
            'phone_number' => 'required',
            'contact_email' => 'required|email',
            'notes' => 'nullable',
            'clone_settings' => 'sometimes|boolean',
            'add_current_user' => 'sometimes|boolean',
            'clone_extensions' => 'sometimes|boolean',
            'clone_custom_fields' => 'sometimes|boolean',
            'users' => 'nullable|array',
        ];
    }
}
