<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class SpeciesGrouping extends Filter
{
    public function name(): string
    {
        return __('Species Grouping');
    }

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
        return $query;
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return [
            'taxon_id' => __('Group common names by taxonomy'),
            'common_name' => __('Group common names as written'),
        ];
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return 'taxon_id';
    }
}
