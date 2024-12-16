<?php

namespace App;

use App\Enums\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class Search
{
    public function whereSearch(Builder $query)
    {
        $this->query = $query;

        $this->arguments->each(
            fn ($value, $key) => $this->renderSearchArgument($key, $value)
        );
    }

    public function renderSearchArgument($key, $value)
    {
        $key = $this->trimTrailingDigits($key);

        if ($this->hasDateValue($key)) {
            $this->query->where($key, format_date($value, 'Y-m-d'));
        } elseif ($this->hasStaticValue($key)) {
            $this->query->where($key, $value);
        } else {
            $value = $this->formatPhones($key, $value);
            $this->query->where($key, 'like', "%{$value}%");
        }
    }

    /**
     * Determine if the provided key stores date values.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasDateValue($key)
    {
        return self::dateFields()->offsetExists($key);
    }

    /**
     * Determine if the provided key stores strict enum link values.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasStaticValue($key)
    {
        return self::selectableFields()->offsetExists($key);
    }

    /**
     * Trim trailing digits from the key. Matching keys are presumed to be part of a checkbox group.
     *
     * @param  string  $key
     * @return string
     */
    private function trimTrailingDigits($key)
    {
        return preg_replace('/\\.\\d+$/ui', '', $key);
    }

    /**
     * Format phone values into its basic parts.
     *
     * @param  string  $key
     * @param  string  $value
     * @return string
     */
    protected function formatPhones($key, $value)
    {
        return Str::contains($key, 'phone') ? sanatize_phone($value) : $value;
    }

    /**
     * WRMD date fields.
     *
     * @var array
     */
    public static function dateFields()
    {
        static $dateFields = null;

        if (is_null($dateFields)) {
            $dateFields = Collection::make(Attribute::cases())->filter(
                fn ($attribute) => $attribute->isDateAttribute()
            )
                ->pluck('value');
        }

        return $dateFields;
    }

    /**
     * WRMD select fields.
     *
     * @var array
     */
    public static function selectableFields()
    {
        static $selectableFields = null;

        if (is_null($selectableFields)) {
            $selectableFields = Collection::make(Attribute::cases())->filter(
                fn ($attribute) => $attribute->hasAttributeOptions()
            )
                ->pluck('value');
        }

        return $selectableFields;
    }
}
