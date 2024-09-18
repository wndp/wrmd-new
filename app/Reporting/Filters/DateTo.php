<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\DateFilter;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class DateTo extends DateFilter
{
    public $periodic = true;

    public function name(): string
    {
        return __('Date To');
    }

    /**
     * Get the filters default value.
     */
    public function default(): string
    {
        return Carbon::now(Wrmd::settings('timezone'))->format('Y-m-d');
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query->whereDate($this->attribute, '<=', Carbon::parse($value)->format('Y-m-d'));
    }
}
