<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;

/**
 * New York, United Sates
 */
class UsNyLog extends AnnualReport
{
    use ReportsOnDispositions;

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
        return 'reports.annual.usNyLog';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'New York Wildlife Rehabilitation Log';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $admissions = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
            ->with(['patient.rescuer', 'patient.exams', 'patient.predictions' => function ($query) {
                $query->where('category', 'UsNyCauseOfDistress');
            }])
            ->orderBy('case_year')
            ->orderBy('admissions.case_id')
            ->get();

        return [
            'year' => $this->year,
            'admissions' => $admissions,
            'settings' => $this->team->settingsStore(),
        ];
    }
}
