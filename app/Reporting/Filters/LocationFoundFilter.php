<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class LocationFoundFilter extends Filter
{
    /**
     * The name of the Vue component to be used for this filter
     */
    public function component(): string
    {
        return 'GenericSelect';
    }

    /**
     * Humanize the filter into a proper name.
     */
    public function name(): string
    {
        return 'Group by Location';
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query;
        //return $query->where('facility', $value);
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return 'city_found';
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return [
            'subdivision_found' => __('State'),
            'city_found' => __('City'),
            'county_found' => __('County'),
        ];
    }
}
