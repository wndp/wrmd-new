<?php

namespace App\Repositories;

use App\Options\Options;

class OptionsStore
{
    /**
     * The merged options.
     *
     * @var array
     */
    public static $options = [];

    /**
     * Get all the defined options.
     */
    public static function all(): array
    {
        return array_merge(
            //app(PatientOptions::class)->toArray(),
            //app(SharingOptions::class)->toArray(),
            static::$options
        );
    }

    /**
     * Register new options.
     */
    public static function merge(Options|array $options, string $key = null): void
    {
        $array = $options instanceof Options ? $options->toArray() : $options;
        $array = is_null($key) ? $array : [$key => $array];

        static::$options = array_merge_recursive(static::$options, $array);
    }
}
