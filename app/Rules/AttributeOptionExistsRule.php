<?php

namespace App\Rules;

use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class AttributeOptionExistsRule implements ValidationRule
{
    private static array $attributeOptionIds = [];

    public function __construct(private AttributeOptionName|array $attributeOptionName) {}

    public static function clearCache(): void
    {
        static::$attributeOptionIds = [];
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty(static::$attributeOptionIds)) {
            static::$attributeOptionIds = AttributeOption::select([
                'id',
                'name',
            ])
                ->get()
                ->pluck('name', 'id')
                ->toArray();
        }

        $attributeOptionNames = array_column(Arr::wrap($this->attributeOptionName), 'value');

        if (
            ! isset(static::$attributeOptionIds[$value]) || ! in_array(static::$attributeOptionIds[$value]->value, $attributeOptionNames)
        ) {
            $fail('validation.in')->translate();
        }
    }
}
