<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Collection;

/**
 * New Jersey, United Sates.
 */
class UsNj extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reporting.reports.annual.usNj';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'New Jersey Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $heldOver = $this->heldOver();
        $summary = $this->applyFilters($this->scopeAcquisitionTotals())->get();

        $heldOverByClass = $heldOver->groupBy(function ($admision) {
            return $admision->patient->species->class;
        });

        $summaryByClass = $summary->groupBy(function ($dispositions) {
            return $dispositions->class;
        });

        return [
            'year' => $this->year,
            'heldOver' => $heldOver,
            'transfers' => $this->transfers(),
            'pending' => $this->pending(),
            'summary' => $summary,
            'heldOverByClass' => $heldOverByClass,
            'summaryByClass' => $summaryByClass,
        ];

        $heldOver->groupBy(function ($admision) {
            return $admision->patient->species->class;
        });
    }

    public function transfers()
    {
        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', 'Transferred')
            ->with('patient')
            ->get();
    }

    public function pending()
    {
        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', 'Pending')
            ->with('patient')
            ->get();
    }

    /**
     * Number of animals in possession held over from last year.
     */
    private function heldOver(): Collection
    {
        return Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->where('case_year', '<', $this->year)
            ->where('disposition', '!=', 'Void')
            ->whereRaw("(`disposition` = 'Pending' or year(`dispositioned_at`) = {$this->year})")
            ->with('patient.species')
            ->get();
    }
}
