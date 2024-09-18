<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use Illuminate\Database\Eloquent\Builder;

/**
 * Wisconsin, United Sates.
 */
class UsWi extends AnnualReport
{
    public $canExport = true;

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
        return 'reports.annual.usWi';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Wisconsin Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $headings = $this->headings();
        $admissions = $this->query()->get()->map(function ($row) {
            return $this->map($row);
        });

        return [
            'year' => $this->year,
            'headings' => $headings,
            'admissions' => $admissions,
        ];
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return [
            'Date Admitted',
            'Species',
            'Sex / Age',
            'Location Obtained',
            'Diagnosis / Cause',
            'Disposition / Date',
            'Location Released / Transfered',
        ];
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return [
            $row->patient->admitted_at_date,
            $row->patient->common_name,
            implode(', ', array_filter([$row->patient->intake_exam->sex, $row->patient->intake_exam->full_age])),
            $row->patient->location_found,
            $row->patient->diagnosis,
            implode(', ', array_filter([format_disposition($row->patient->disposition), $row->patient->dispositioned_at_date])),
            $row->patient->disposition !== 'Pending' ? $row->patient->disposition_locale : '',
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        parent::data();

        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->with('patient.exams')
            ->orderBy('case_year')
            ->orderBy('admissions.case_id');
    }
}
