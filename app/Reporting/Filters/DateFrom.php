<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\DateFilter;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class DateFrom extends DateFilter
{
    public $periodic = true;

    public function name(): string
    {
        return __('Date From');
    }

    /**
     * Get the filters default value.
     */
    public function default(): string
    {
        return (new Carbon())->firstOfYear()->format('Y-m-d');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query->whereDate($this->attribute, '>=', Carbon::parse($value)->format('Y-m-d'));
    }
}
