<?php

namespace App\Reporting\Reports\Disposition;

use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\IncludedTaxonomies;
use App\Reporting\Filters\SpeciesGrouping;
use App\Reporting\ReportsOnDispositions;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReleaseTypesBySpecies extends Report
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.disposition.release-types-by-species';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Release Types by Species');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect(array_merge((new DateRange('dispositioned_at'))->toArray(), [
            new IncludedTaxonomies(),
            new SpeciesGrouping(),
        ]));
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class, (new IncludedTaxonomies())->default());

        $releaseTypes = AttributeOption::getDropdownOptions([
            AttributeOptionName::PATIENT_RELEASE_TYPES->value,
        ])->first();

        return [
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
            'map' => [
                'amphibian' => in_array('Amphibia', $includedTaxonomies) ? $this->applyFilters($this->scopeReleaseTypeTotals('Amphibia'))->get() : [],
                'bird' => in_array('Aves', $includedTaxonomies) ? $this->applyFilters($this->scopeReleaseTypeTotals('Aves'))->get() : [],
                'mammal' => in_array('Mammalia', $includedTaxonomies) ? $this->applyFilters($this->scopeReleaseTypeTotals('Mammalia'))->get() : [],
                'reptile' => in_array('Reptilia', $includedTaxonomies) ? $this->applyFilters($this->scopeReleaseTypeTotals('Reptilia'))->get() : [],
                'unidentified' => in_array('Unidentified', $includedTaxonomies) ? $this->applyFilters($this->scopeReleaseTypeTotals('Unidentified'))->get() : [],
            ],
            'grand' => $this->applyFilters($this->scopeReleaseTypeTotals($includedTaxonomies))->get(),
            'releaseTypes' => $releaseTypes
        ];
    }
}
