<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;

/**
 * Alabama, United Sates
 */
class UsAl extends AnnualReport
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
        return 'reports.annual.usAl';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Alabama Wildlife Rehabilitation Annual Report';
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
            ->where('disposition', '!=', 'Void')
            ->with('patient')
            ->get();

        return [
            'year' => $this->year,
            'admissions' => $admissions,
        ];
    }
}
