<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Nz extends AnnualReport
{
    /**
     * Get the pdf format options.
     */
    public function options(): array
    {
        return [
            'viewportSize' => 'auto',
            'viewportMargin' => '0 20px',
            'orientation' => 'landscape',
            'user-style-sheet' => public_path('css/report-landscape.css'),
        ];
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.nz';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'New Zealand Annual Report';
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
            'admissions' => Admission::where('team_id', $this->team->id)
                ->select('admissions.*')
                ->joinPatients()
                ->where('is_void', false)
                ->dateRange(($this->year - 1).'-07-01', $this->year.'-06-31')
                ->orderBy('patients.admitted_at')
                ->with('patient.exams')
                ->get(),
            'dateFrom' => Carbon::parse(($this->year - 1).'-07-01', $this->team->settingsStore()->get('timezone'))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->year.'-06-30', $this->team->settingsStore()->get('timezone'))->format(config('wrmd.date_format')),
        ];
    }
}
