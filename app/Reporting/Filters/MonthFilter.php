<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class MonthFilter extends Filter
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
        return 'Month';
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ];
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return now()->format('m');
    }
}
