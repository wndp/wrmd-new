<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class LocationArea extends Filter
{
    public function name(): string
    {
        return __('Location Area');
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query->where('area', $value);
    }
}
