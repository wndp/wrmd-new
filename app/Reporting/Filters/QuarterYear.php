<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class QuarterYear extends Filter
{
    public $periodic = true;

    /**
     * The name of the Vue component to be used for this filter
     */
    public function component(): string
    {
        return 'GenericSelect';
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    public function apply(Fluent $request, Builder $query, $value): Builder
    {
    }

    public function name(): string
    {
        return 'Quarter of the Year';
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return 1;
    }

    public function options(): array
    {
        return [
            1 => 'First',
            2 => 'Second',
            3 => 'Third',
            4 => 'Fourth',
        ];
    }
}
