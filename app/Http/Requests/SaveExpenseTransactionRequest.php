<?php

namespace App\Http\Requests;

use App\Enums\SettingKey;
use App\Enums\AttributeOptionName;
use App\Rules\AttributeOptionExistsRule;
use App\Support\Wrmd;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class SaveExpenseTransactionRequest extends FormRequest
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
        Validator::extendImplicit('require_debit_or_credit', function ($attribute, $value, $parameters, $validator) {
            $debit = Arr::get($validator->getData(), 'debit');
            $credit = Arr::get($validator->getData(), 'credit');

            return count(array_filter([$debit, $credit])) === 1;
        }, __('A debit or credit is required.'));

        $patient = $this->route('patient')->validateOwnership($this->user()->current_team_id);
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        return [
            'transacted_at' => 'required|date|after_or_equal:'.$admittedAt,
            'category' => [
                'required',
                Rule::exists('expense_categories', 'name')->where(function ($query) {
                    $query->where(function ($query) {
                        $query->whereNull('team_id')->whereNull('parent_id');
                    })
                        ->orWhere('team_id', $this->user()->current_team_id);
                }),
            ],
            'charge' => 'require_debit_or_credit',
            'debit' => 'nullable|numeric',
            'credit' => 'nullable|numeric',
        ];
    }

    public function messages()
    {
        $patient = $this->route('patient');
        $admittedAt = $patient->admitted_at->setTimezone(Wrmd::settings(SettingKey::TIMEZONE))->startOfDay();

        return [
            'transacted_at.required' => __('The transaction date field is required.'),
            'transacted_at.date' => __('The transaction date is not a valid date.'),
            'category.required' => __('A category is required.'),
            'category.exists' => __('The selected category is invalid.'),
        ];
    }
}
