<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;

/**
 * Maryland, United Sates
 */
class UsMd extends AnnualReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usMd';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Maryland Wildlife Rehabilitation Annual Report';
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
