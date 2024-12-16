<?php

namespace App\Reporting\Filters;

use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Options\Options;
use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class WhereHolding extends Filter
{
    /**
     * The name of the Vue component to be used for this filter
     */
    public function component(): string
    {
        return 'GenericSelect';
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query->where('facility', $value);
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return 'Clinic';
    }

    /**
     * Get the filters render options.
     */
    public function options(): array
    {
        return AttributeOption::getDropdownOptions([
            AttributeOptionName::PATIENT_LOCATION_FACILITIES->value,
        ])->first();
    }
}
