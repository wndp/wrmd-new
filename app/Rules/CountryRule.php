<?php

namespace App\Rules;

use App\Repositories\AdministrativeDivision;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CountryRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! array_key_exists($value, app(AdministrativeDivision::class)->countries())) {
            $fail('validation.in')->translate();
        }
    }
}
