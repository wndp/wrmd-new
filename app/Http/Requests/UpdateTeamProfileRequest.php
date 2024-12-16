<?php

namespace App\Http\Requests;

use App\Enums\AccountStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamProfileRequest extends FormRequest
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
            'status' => [
                'required',
                Rule::enum(AccountStatus::class),
            ],
            'is_master_account' => 'nullable|boolean',
            'name' => 'required',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'subdivision' => 'required',
            'postal_code' => 'nullable',
            'contact_name' => 'required',
            'phone_number' => 'required',
            'contact_email' => 'required|email',
            'website' => 'nullable|url',
            'federal_permit_number' => 'nullable',
            'subdivision_permit_number' => 'nullable',
            'notes' => 'nullable',
        ];
    }
}
