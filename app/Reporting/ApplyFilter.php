<?php

namespace App\Reporting;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class ApplyFilter
{
    /**
     * The filter instance.
     *
     * @var \Laravel\Nova\Filters\Filter
     */
    public $filter;

    /**
     * The value of the filter.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new invokable filter applier.
     *
     * @param  mixed  $value
     * @return void
     */
    public function __construct(Filter $filter, $value)
    {
        $this->value = $value;
        $this->filter = $filter;
    }

    /**
     * Apply the filter to the given query.
     */
    public function __invoke(Fluent $request, Builder $query): Builder
    {
        $this->filter->apply(
            $request,
            $query,
            $this->value
        );

        return $query;
    }
}
