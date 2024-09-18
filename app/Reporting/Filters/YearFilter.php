<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class YearFilter extends Filter
{
    public $periodic = true;

    protected $years;

    public function __construct($years)
    {
        $this->years = $years;
    }

    /**
     * The name of the Vue component to be used for this filter
     */
    public function component(): string
    {
        return 'GenericSelect';
    }

    public function options(): array
    {
        return $this->years
            ->mapWithKeys(function ($year) {
                return [$year => $year];
            })
            ->toArray();
    }

    public function name(): string
    {
        return 'Year';
    }

    public function default()
    {
        return $this->years->first();
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query->where('case_year', $value);
    }
}
