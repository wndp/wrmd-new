<?php

namespace App\Rules;

use App\Enums\Role as RoleEnum;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Role implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_null(RoleEnum::tryFrom($value))) {
            $fail(__('The :attribute must be a valid role.'));
        }
    }
}
