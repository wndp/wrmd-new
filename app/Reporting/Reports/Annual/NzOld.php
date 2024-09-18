<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\FiscalYear;
use App\Reporting\ReportsOnDispositions;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NzOld extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.nzOld';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'New Zealand Annual Report (Old)';
    }

    /**
     * Get the reports filters.
     */
    public function filters(): Collection
    {
        return new Collection([
            new FiscalYear(Admission::yearsInAccount($this->team->id)),
        ]);
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $this->year = $this->getAppliedFilterValue(FiscalYear::class);

        return [
            'acquisitions' => $this->applyFilters($this->scopeAcquisitionTotals('Aves'))->get(),
            'dateFrom' => Carbon::parse(($this->year - 1).'-07-01', $this->team->settingsStore()->get('timezone'))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->year.'-06-30', $this->team->settingsStore()->get('timezone'))->format(config('wrmd.date_format')),
        ];
    }
}
