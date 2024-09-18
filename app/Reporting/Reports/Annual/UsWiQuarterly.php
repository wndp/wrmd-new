<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\QuarterYear;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Wisconsin, United Sates.
 */
class UsWiQuarterly extends AnnualReport
{
    public $canExport = true;

    protected $quarters;

    protected $whichQuarter;

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
        return 'Wisconsin Wildlife Rehabilitation Quarterly Report';
    }

    /**
     * Get the reports filters.
     */
    public function filters(): Collection
    {
        return parent::filters()->push(
            new QuarterYear()
        );
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
            'dateFrom' => Carbon::parse($this->quarters[$this->whichQuarter][0])->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->quarters[$this->whichQuarter][1])->format(config('wrmd.date_format')),
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

        $this->whichQuarter = intval($this->getAppliedFilterValue(QuarterYear::class));

        $this->quarters = [
            '1' => [$this->year.'-01-01', $this->year.'-03-31'],
            '2' => [$this->year.'-04-01', $this->year.'-06-30'],
            '3' => [$this->year.'-07-01', $this->year.'-09-30'],
            '4' => [$this->year.'-10-01', $this->year.'-12-31'],
        ];

        $quarter = $this->quarters[$this->whichQuarter];

        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->with('patient.exams')
            ->orderBy('case_year')
            ->orderBy('admissions.case_id')
            ->joinTaxa()
            ->where(function ($query) {
                $query->whereIn('taxa.family', ['Canidae', 'Ursidae', 'Mustelidae', 'Felidae', 'Cervidae', 'Suidae', 'Mephitidae'])
                    ->orWhere(function ($query) {
                        $query->where('genus', 'Cygnus')->where('species', 'olor');
                    });
            })
            ->where(function ($query) use ($quarter) {
                // where admitted in this quarter
                $query->dateRange($quarter[0], $quarter[1])
                    // or pending
                    ->orWhere('disposition', 'Pending')
                    ->orWhere(function ($query) use ($quarter) {
                        // or dispositioned in the reporting quarter
                        $query->dateRange($quarter[0], $quarter[1], 'dispositioned_at');
                    });
            });
    }
}
