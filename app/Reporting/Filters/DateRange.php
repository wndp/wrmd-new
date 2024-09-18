<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class DateRange extends Filter
{
    public $periodic = true;

    protected $attribute;

    public function __construct($attribute = 'date_admitted_at')
    {
        $this->attribute = $attribute;
    }

    public function name(): string
    {
        return __('Date Range');
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query;
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return 'this-year';
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return [
            'today' => 'Today',
            'this-week' => 'This week',
            'this-month' => 'This month',
            'this-year' => 'This year',
            'yesterday' => 'Yesterday',
            'last-week' => 'Last week',
            'last-week-to-date' => 'Last week-to-date',
            'last-month' => 'Last month',
            'last-month-to-date' => 'Last month-to-date',
            'last-year' => 'Last year',
            'last-year-to-date' => 'Last year-to-date',
            'custom' => 'Custom',
        ];
    }

    public function toArray()
    {
        return [
            $this,
            new DateFrom($this->attribute),
            new DateTo($this->attribute),
        ];
    }
}
