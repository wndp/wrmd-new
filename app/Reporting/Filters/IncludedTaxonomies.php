<?php

namespace App\Reporting\Filters;

use App\Options\Options;
use App\Reporting\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class IncludedTaxonomies extends Filter
{
    public function name(): string
    {
        return __('Included Taxonomies');
    }

    public function default()
    {
        return Options::valueAsKey([
            'Amphibia',
            'Aves',
            'Mammalia',
            'Reptilia',
        ]);
    }

    public function options(): array
    {
        return $this->default();
    }

    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        return $query;
    }
}
