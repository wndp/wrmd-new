<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait QueriesOneOfMany
{
    public function scopeLikeOneOfMany(Builder $query, string $term, array $fields): Builder
    {
        return $query->whereRaw(
            "CONCAT_WS(' ',".
            implode(',', array_map('protect_identifiers', $fields)).
            ') like ?',
            ["%$term%"]
        );
    }
}
