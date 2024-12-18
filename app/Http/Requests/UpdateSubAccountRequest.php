<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubAccountRequest extends FormRequest
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
            'phone' => 'required|phone:country',
            'contact_email' => 'required|email',
            'notes' => 'nullable',
            'federal_permit_number' => 'nullable',
            'subdivision_permit_number' => 'nullable',
        ];
    }
}
