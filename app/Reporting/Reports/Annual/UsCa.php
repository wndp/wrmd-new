<?php

namespace App\Reporting\Reports\Annual;

use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Facades\DB;

/**
 * California, United Sates
 */
class UsCa extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usCa';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'California Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $reunited = DB::raw("sum(if(`disposition` = 'Released' && release_type = 'Reunite with family', 1, 0)) as `reunited`");

        return [
            'year' => $this->year,
            'amphibianAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Amphibia'))->addSelect($reunited)->get(),
            'birdAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Aves'))->addSelect($reunited)->get(),
            'mammalAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Mammalia'))->addSelect($reunited)->get(),
            'reptileAcquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Reptilia'))->addSelect($reunited)->get(),
        ];
    }
}
