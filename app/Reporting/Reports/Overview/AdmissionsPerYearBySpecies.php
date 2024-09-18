<?php

namespace App\Reporting\Reports\Overview;

use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\IncludedTaxonomies;
use App\Reporting\Filters\SpeciesGrouping;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class AdmissionsPerYearBySpecies extends Report
{
    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return __('Patients Per Year by Species');
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return __('The Patients per Year by Species report provides a 5 year comparison of the total species that were admitted each year.');
    }

    /**
     * {@inheritdoc}
     */
    public function viewPath(): string
    {
        return 'reports.overview.admissions-per-year-by-species';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect([
            new IncludedTaxonomies(),
            new SpeciesGrouping(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function data(): array
    {
        $this->years = Admission::yearsInAccount($this->team->id)->slice(0, 5)->reverse();
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class);

        return [
            'map' => [
                'amphibian' => in_array('Amphibia', $includedTaxonomies) ? $this->admissionsTotals('Amphibia') : [],
                'bird' => in_array('Aves', $includedTaxonomies) ? $this->admissionsTotals('Aves') : [],
                'mammal' => in_array('Mammalia', $includedTaxonomies) ? $this->admissionsTotals('Mammalia') : [],
                'reptile' => in_array('Reptilia', $includedTaxonomies) ? $this->admissionsTotals('Reptilia') : [],
                'unidentified' => in_array('Unidentified', $includedTaxonomies) ? $this->admissionsTotals('Unidentified') : [],
            ],
            'years' => $this->years,
            'grand' => $this->admissionsTotals($includedTaxonomies),
        ];
    }

    /**
     * Common query arguments for section B of US form 3-202-4.
     */
    protected function admissionsTotals($class = null): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('case_year', 'common_name', 'taxon_id', 'admissions.case_id')
            ->joinPatients()
            ->whereIn('case_year', $this->years)
            ->orderBy('common_name');

        if ($class) {
            $query->joinTaxa()->whereIn('class', Arr::wrap($class));
        }

        return $query
            ->get()
            ->groupBy($this->getAppliedFilterValue(SpeciesGrouping::class))
            ->keyBy(function ($collection) {
                return $collection->first()->common_name;
            });
    }
}
