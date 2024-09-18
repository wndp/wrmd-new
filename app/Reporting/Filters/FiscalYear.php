<?php

namespace App\Reporting\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class FiscalYear extends YearFilter
{
    public $periodic = true;

    /**
     * The name of the Vue component to be used for this filter
     */
    public function component(): string
    {
        return 'GenericSelect';
    }

    public function name(): string
    {
        return 'Fiscal Year Ending In';
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        $dates = [($value - 1).'-07-01', $value.'-06-31'];

        return $query->dateRange($dates[0], $dates[1]);
    }
}
