<?php

namespace App\Reporting\Reports\Prescriptions;

use App\DailyTasks\Prescriptions\Prescription;
use App\Reporting\Contracts\Report;

class PrescriptionLabels extends Report
{
    /**
     * Get the pdf format options.
     */
    public function options(): array
    {
        return [
            'pageSize' => 'A5',
            'orientation' => 'landscape',
            'no-header' => true,
            'no-footer' => true,
        ];
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.prescriptions.labels';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Prescription Labels';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $prescriptions = Prescription::with('patient')->whereIn('id', \Request::get('rx', []))->get();

        return compact('prescriptions');
    }
}
