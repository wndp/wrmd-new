<?php

namespace App\Concerns;

trait ReadOnlyModel
{
    public static function bootReadOnlyModel()
    {
        static::creating(fn ($model) => false);
        static::updating(fn ($model) => false);
        static::saving(fn ($model) => false);
        static::deleting(fn ($model) => false);
    }
}
