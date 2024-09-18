<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\DateFilter;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class DateOn extends DateFilter
{
    public $periodic = true;

    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query->whereDate($this->attribute, Carbon::parse($value)->format('Y-m-d'));
    }

    /**
     * Get the filters default value.
     */
    public function default(): string
    {
        return now(Wrmd::settings('timezone'))->format('Y-m-d');
    }

    public function name(): string
    {
        return 'Date';
    }
}
