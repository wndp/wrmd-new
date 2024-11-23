<?php

namespace App\Repositories;

use App\Options\Options;
use App\Support\AttributeOptionsCollection;

class OptionsStore
{
    /**
     * The merged options.
     *
     * @var array
     */
    public static $options = [];

    public static function clearCache()
    {
        static::$options = [];
    }

    /**
     * Get all the defined options.
     */
    public static function all(): array
    {
        return array_merge(
            static::$options
        );
    }

    public static function add(Options|array $options): void
    {
        $array = $options instanceof Options ? $options->toArray() : $options;

        foreach ($array as $key => $value) {
            if ($value instanceof Options) {
                static::$options = array_merge_recursive(static::$options, $value->toArray());
            } elseif ($value instanceof AttributeOptionsCollection) {
                static::$options = array_merge_recursive(static::$options, $value->optionsToSelectable());
            } else {
                static::$options = array_merge_recursive(static::$options, [$key => $value]);
            }
        }
    }
}
