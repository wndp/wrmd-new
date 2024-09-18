<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;

class Ca extends AnnualReport
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
        return 'reports.annual.ca';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Canada (Atlantic Region) Rehabilitation Report Form';
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        parent::data();

        $admissions = Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->where('is_mbta', '1')
            ->orderBy('patients.admitted_at')
            ->with('patient')
            ->get();

        return [
            'year' => $this->year,
            'admissions' => $admissions,
        ];
    }
}
