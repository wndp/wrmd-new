<?php

namespace App\Rules;

use App\Repositories\AdministrativeDivision;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class SubdivisionRule implements DataAwareRule, ValidationRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    public function __construct(public ?string $country = null)
    {
        $this->country = $country;
    }

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $country = $this->country ?? $this->data['country'] ?? null;

        if (! array_key_exists($value, app(AdministrativeDivision::class)->countrySubdivisions($country))) {
            $fail('validation.in')->translate();
        }
    }
}
