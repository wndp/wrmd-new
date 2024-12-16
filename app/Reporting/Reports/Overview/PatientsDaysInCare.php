<?php

namespace App\Reporting\Reports\Overview;

use App\Listing\ListingQuery;
use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\IncludedTaxonomies;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class PatientsDaysInCare extends Report
{
    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return 'This report provides the number of days each patient was in care for all patients admitted during the chosen date range.';
    }

    /**
     * {@inheritdoc}
     */
    public function viewPath(): string
    {
        return 'reports.overview.patients-days-in-care';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge(array_merge((new DateRange)->toArray(), [
            new IncludedTaxonomies,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function data(): array
    {
        $this->years = Admission::yearsInAccount($this->team->id)->slice(0, 5)->reverse();
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class);

        return [
            'admissions' => new Collection([
                'amphibian' => in_array('Amphibia', $includedTaxonomies) ? $this->applyFilters($this->query('Amphibia'))->get() : collect(),
                'bird' => in_array('Aves', $includedTaxonomies) ? $this->applyFilters($this->query('Aves'))->get() : collect(),
                'mammal' => in_array('Mammalia', $includedTaxonomies) ? $this->applyFilters($this->query('Mammalia'))->get() : collect(),
                'reptile' => in_array('Reptilia', $includedTaxonomies) ? $this->applyFilters($this->query('Reptilia'))->get() : collect(),
                'unidentified' => in_array('Unidentified', $includedTaxonomies) ? $this->applyFilters($this->query('Unidentified'))->get() : collect(),
            ]),
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
        ];
    }

    private function query($class)
    {
        return ListingQuery::run()
            ->selectAdmissionKeys()
            ->joinTaxa()
            ->where('team_id', $this->team->id)
            ->whereIn('class', Arr::wrap($class))
            ->orderBy('admissions.case_year')
            ->orderBy('admissions.case_id')
            ->with('patient');

        // return Admission::where('team_id', $this->team->id)
        //     ->joinPatients()
    }
}
