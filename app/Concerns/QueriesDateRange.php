<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait QueriesDateRange
{
    public function scopeDateRange(Builder $query, $start, $end, string $field): Builder
    {
        if (is_null($start) && is_null($end)) {
            return $query;
        } elseif (is_null($start)) {
            return $query->whereDate($field, '<=', $end);
        } elseif (is_null($end)) {
            return $query->whereDate($field, '>=', $start);
        } elseif ($start === $end) {
            return $query->whereDate($field, '=', $start);
        }

        //$field = protect_identifiers($field);

        return $query->whereRaw("$field between '$start' and '$end 23:59:59.999999'");
    }
}
