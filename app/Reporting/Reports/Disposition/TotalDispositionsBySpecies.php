<?php

namespace App\Reporting\Reports\Disposition;

use App\Reporting\Contracts\ExportableReport;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\IncludedTaxonomies;
use App\Reporting\Filters\SpeciesGrouping;
use App\Reporting\ReportsOnDispositions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TotalDispositionsBySpecies extends ExportableReport implements WithMultipleSheets
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.disposition.total-dispositions-by-species';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Dispositions by Species');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge(array_merge((new DateRange())->toArray(), [
            new IncludedTaxonomies(),
            new SpeciesGrouping(),
        ]));
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $sheets = new Collection($this->sheets());
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class, (new IncludedTaxonomies())->default());

        return [
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
            'headings' => $sheets->first()->headings(),
            'collections' => $sheets->map(function ($sheet) {
                return $sheet->collection();
            }),
            'grand' => $this->applyFilters($this->scopeAcquisitionTotals($includedTaxonomies))->get(),
        ];
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->getAppliedFilterValue(IncludedTaxonomies::class, (new IncludedTaxonomies())->default()) as $class) {
            $sheets[$class] = (new DispositionStatsPerTaxonomySheet($this->team))
                ->withFilters($this->appliedFilters)
                ->withRequest(new Request(compact('class')));
        }

        return $sheets;
    }
}
