<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\FiscalYear;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Pennsylvania, United Sates
 */
class UsPa extends AnnualReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usPa';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Pennsylvania Wildlife Rehabilitation Annual Report';
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
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $this->year = $this->getAppliedFilterValue(FiscalYear::class);

        $dates = [($this->year - 1).'-07-01', $this->year.'-06-31'];

        return [
            'year' => $this->year,
            'dateFrom' => Carbon::parse($dates[0], $this->team->settingsStore()->get('timezone'))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($dates[1], $this->team->settingsStore()->get('timezone'))->format(config('wrmd.date_format')),
            'admissions' => $this->admissions($dates),
            'mammals' => $this->mammals($dates),
            'passerines' => $this->passerines($dates),
            'raptors' => $this->raptors($dates),
        ];
    }

    /**
     * Get all admissions.
     */
    private function admissions($dates): Collection
    {
        return Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->dateRange($dates[0], $dates[1])
            ->orderBy('admitted_at')
            ->with('patient.rescuer', 'patient.exams')
            ->get();
    }

    /**
     * Get the mammals.
     */
    private function mammals($dates): int
    {
        return Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->where('class', 'Mammalia')
            ->dateRange($dates[0], $dates[1])
            ->orderBy('admitted_at')
            ->count();
    }

    /**
     * Get the passerines.
     */
    private function passerines($dates): int
    {
        return Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->where('order', 'Passeriformes')
            ->dateRange($dates[0], $dates[1])
            ->orderBy('admitted_at')
            ->count();
    }

    /**
     * Get the raptors.
     */
    private function raptors($dates): int
    {
        return Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->where('lay_groups', 'like', '%raptor%')
            ->dateRange($dates[0], $dates[1])
            ->orderBy('admitted_at')
            ->count();
    }
}
