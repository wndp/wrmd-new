<?php

namespace App\Reporting\Reports\Annual;

use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Facades\DB;

/**
 * Virginia, United Sates
 */
class UsVa extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usVa';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Virginia Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $care = DB::raw("sum(if(`transfer_type` like 'Continued%', 1, 0)) as `care`");
        $education = DB::raw("sum(if(`transfer_type` like 'Education%', 1, 0)) as `education`");
        $surrogates = DB::raw("sum(if(`transfer_type` like 'Falconry%', 1, 0)) as `surrogates`");

        return [
            'year' => $this->year,

            'amphibianAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Amphibia'))
                ->addSelect($care)
                ->addSelect($education)
                ->addSelect($surrogates)
                ->get(),

            'birdAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Aves'))
                ->addSelect($care)
                ->addSelect($education)
                ->addSelect($surrogates)
                ->get(),

            'mammalAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Mammalia'))
                ->addSelect($care)
                ->addSelect($education)
                ->addSelect($surrogates)
                ->get(),

            'reptileAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Reptilia'))
                ->addSelect($care)
                ->addSelect($education)
                ->addSelect($surrogates)
                ->get(),
        ];
    }
}
