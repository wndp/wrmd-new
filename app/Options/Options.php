<?php

namespace App\Options;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class Options implements Arrayable
{
    public static function enumsToSelectable(array $enums)
    {
        $array = Arr::mapWithKeys($enums, fn ($enum) => [$enum->value => $enum->label()]);

        return static::arrayToSelectable($array);
    }

    /**
     * Get the transformed options as an array.
     */
    public function toArray(): array
    {
        return collect($this->getClassStaticVars())
            ->transform(fn ($array) => static::arrayToSelectable($array))
            ->toArray();
    }

    /**
     * Get the static properties of the Options class.
     */
    public function getClassStaticVars(): array
    {
        return array_diff_key(
            get_class_vars(get_class($this)),
            get_object_vars($this)
        );
    }

    /**
     * Transform an array into an HTML select input "friendly" / iterable object.
     */
    public static function arrayToSelectable(array $array): array
    {
        if (static::isMultidimensional($array)) {
            return collect($array)->transform(function ($array, $label) {
                if (! is_array($array)) {
                    return static::arrayToSelectable(Arr::wrap($array));
                }

                return [
                    'label' => $label,
                    'group' => static::arrayToSelectable($array),
                ];
            })->values()->toArray();
        }

        if (! Arr::isAssoc($array)) {
            $array = static::valueAsKey($array);
        }

        return collect($array)->transform(function ($label, $value) {
            return compact('label', 'value');
        })->values()->toArray();
    }

    /**
     * Determine if an array is multidimensional.
     */
    public static function isMultidimensional(array $array): bool
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    /**
     * Create an array where the values are also the keys.
     *
     * @param  bool  $nullFirstOption
     * @return array
     */
    public static function valueAsKey(array $oldArray = [], $nullFirstOption = false)
    {
        $newArray = [];

        if (empty($oldArray)) {
            return $newArray;
        }

        if ($nullFirstOption) {
            $newArray[''] = '';
        }

        if (is_array($oldArray[key($oldArray)])) {
            $newArray += array_map(function ($array) {
                return static::valueAsKey($array);
            }, $oldArray);
        } else {
            $newArray += array_combine($oldArray, $oldArray);
        }

        return $newArray;
    }
}
