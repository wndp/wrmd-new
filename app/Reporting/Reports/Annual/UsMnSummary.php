<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use Illuminate\Support\Str;

/**
 * Minnesota, United Sates
 */
class UsMnSummary extends AnnualReport
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
        return 'reporting.reports.annual.usMnSummary';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Minnesota Summary Annual Report';
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
            ->with('patient.species')
            ->get()
            ->transform(function ($admission) {
                $admission->patient->disposition_for_minnesota = $this->dispositionForMinnesota($admission->patient);

                return $admission;
            });

        return [
            'year' => $this->year,
            'admissions' => $admissions,
        ];
    }

    public function dispositionForMinnesota($patient)
    {
        if ($patient->disposition === 'Dead on arrival') {
            return 'DOA';
        } elseif ($patient->disposition === 'Euthanized in 24hr') {
            return 'EA';
        } elseif ($patient->disposition === 'Euthanized +24hr') {
            return 'ET';
        } elseif (Str::startsWith($patient->disposition, 'Died')) {
            return 'D';
        } elseif ($patient->disposition === 'Released') {
            return 'R';
        } elseif ($patient->disposition === 'Transferred' && $patient->transer_type === 'Continued care') {
            return 'TR';
        } elseif ($patient->disposition === 'Transferred' && Str::startsWith($patient->transer_type, 'Education')) {
            return 'TE';
        } elseif ($patient->disposition === 'Pending') {
            return 'P';
        }
    }
}
