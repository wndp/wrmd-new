<?php

namespace App\Concerns;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * @link https://medium.com/@neuland/unique-columns-with-singlestore-and-laravel-2cd781e8ec69
 */
trait HasUniqueFields
{
    public static function bootHasUniqueFields(): void
    {
        static::saving(function (Model $model) {
            collect(isset($model->unique) ? (array) $model->unique : [])
                ->each(fn (string $field) => static::checkUsingAtomicLock($model, $field));
        });
    }

    private static function checkUsingAtomicLock(Model $model, string $field): void
    {
        Cache::lock($model::class.'-'.$field.':'.$model->{$field}, 60)
            ->block(30, function () use ($field, $model) {
                throw_if(static::fieldIsNotUnique($model, $field), new Exception('Field must be unique'));
            });
    }

    private static function fieldIsNotUnique(Model $model, string $field): bool
    {
        return app($model::class)::where($field, $model->{$field})
            ->whereNot($model->getKeyName(), $model->getKey())
            ->exists();
    }
}
