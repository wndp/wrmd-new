<?php

namespace App\Concerns;

use Illuminate\Support\Fluent;

trait AsAction
{
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @see static::handle()
     *
     * @param  mixed  ...$arguments
     * @return mixed
     */
    public static function run(...$arguments)
    {
        return static::make()->handle(...$arguments);
    }

    /**
     * @return mixed|\Illuminate\Support\Fluent
     */
    public static function runIf($boolean, ...$arguments)
    {
        return $boolean ? static::run(...$arguments) : new Fluent();
    }

    /**
     * @return mixed|\Illuminate\Support\Fluent
     */
    public static function runUnless($boolean, ...$arguments)
    {
        return static::runIf(! $boolean, ...$arguments);
    }
}
