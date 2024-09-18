<?php

namespace App\Reporting\Filters;

use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class SortBy extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    public function apply(Fluent $request, Builder $query, $value): Builder
    {
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return [
            'admissions_id' => __('Case Number'),
            'patients.reference_number' => __('Reference Number'),
            'patients_common_name' => __('Common Name'),
            'patients_admitted_at' => __('Date Admitted'),
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
