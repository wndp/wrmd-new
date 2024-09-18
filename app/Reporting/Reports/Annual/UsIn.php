<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;

/**
 * Indiana, United Sates
 */
class UsIn extends AnnualReport
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
        return 'reports.annual.usIn';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Indiana Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $admissions = Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->where('class', '!=', 'Aves')
            ->with('patient.rescuer')
            ->get();

        return [
            'year' => $this->year,
            'admissions' => $admissions,
        ];
    }
}
