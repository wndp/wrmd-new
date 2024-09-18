<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Facades\DB;

/**
 * Colorado, United Sates
 */
class UsCo extends AnnualReport
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
        return 'reports.annual.usCo';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Colorado Wildlife Rehabilitation Annual Report';
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
            ->with('patient.exams')
            ->get();

        $priorAdmissions = Admission::owner($this->team->id, ($this->year - 1))
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->whereRaw('year(dispositioned_at) = '.$this->year)
            ->with('patient.exams')
            ->get();

        $summary = $this->scopeAcquisitionTotals()
            ->addSelect(DB::raw("sum(if(`disposition` = 'Transferred' and date(admitted_at) = date(dispositioned_at), 1, 0)) as `transferred_in_24`"))
            ->addSelect(DB::raw("sum(if(`disposition` = 'Transferred' and date(admitted_at) != date(dispositioned_at), 1, 0)) as `transferred`"))
            ->where(function ($query) {
                $query->where('case_year', $this->year)
                    ->orWhereRaw('year(dispositioned_at) = '.$this->year);
            })
            ->get();

        return [
            'year' => $this->year,
            'admissions' => $admissions,
            'priorAdmissions' => $priorAdmissions,
            'summary' => $summary,
        ];
    }
}
