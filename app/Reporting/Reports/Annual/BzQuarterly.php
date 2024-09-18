<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\QuarterYear;
use App\Reporting\ReportsOnDispositions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Belize.
 */
class BzQuarterly extends AnnualReport implements WithMultipleSheets
{
    use ReportsOnDispositions;

    public $canExport = true;

    protected $whichQuarter;

    protected $start;

    protected $end;

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Belize Wildlife Rehabilitation Quarterly Report';
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reporting.reports.annual.bzQuarterly';
    }

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
        return array_merge($this->sheets(), [
            'year' => $this->year,
            'dateFrom' => Carbon::parse($this->quarters[$this->whichQuarter][0])->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->quarters[$this->whichQuarter][1])->format(config('wrmd.date_format')),
        ]);
    }

    public function sheets(): array
    {
        parent::data();

        $this->whichQuarter = intval($this->getAppliedFilterValue(QuarterYear::class));

        $this->quarters = [
            '1' => [$this->year.'-01-01', $this->year.'-03-31'],
            '2' => [$this->year.'-04-01', $this->year.'-06-30'],
            '3' => [$this->year.'-07-01', $this->year.'-09-30'],
            '4' => [$this->year.'-10-01', $this->year.'-12-31'],
        ];

        $this->start = $this->quarters[$this->whichQuarter][0];
        $this->end = $this->quarters[$this->whichQuarter][1];

        $sheets['acquisitionTotals'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Acquisition Totals',
                'headings' => ['Species', 'Total', 'R', 'T', 'P', 'EIC', 'EOA', 'D', 'DIC', 'DOA'],
                'rows' => $this->getAcquisitionTotals(),
            ]));

        $sheets['intake'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Intake',
                'headings' => ['Case Number', 'Common Name', 'Band #', 'Age', 'Sex', 'Address Found', 'District Found', 'Admitted By',  'Admit Date', 'Circumstances of Admission', 'Diagnosis', 'Disposition', 'Disposition Date', 'Disposition Location'],
                'rows' => $this->getIntakes(),
            ]));

        $sheets['deaths'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Deaths',
                'headings' => ['Case Number', 'Common Name', 'Band #', 'Age', 'Sex', 'Address Found', 'District Found', 'Admitted By',  'Admit Date', 'Circumstances of Admission', 'Diagnosis', 'Disposition', 'Disposition Date'],
                'rows' => $this->getDeaths(),
            ]));

        $sheets['transfers'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Transferred Out',
                'headings' => ['Case Number', 'Common Name', 'Band #', 'Age', 'Sex', 'Address Found', 'District Found', 'Admitted By',  'Admit Date', 'Circumstances of Admission', 'Diagnosis', 'Disposition', 'Transfer Type', 'Transfer Date', 'Disposition Location'],
                'rows' => $this->getTransfers(),
            ]));

        $sheets['releases'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Releases',
                'headings' => ['Case Number', 'Common Name', 'Band #', 'Age', 'Sex', 'Address Found', 'District Found', 'Admitted By',  'Admit Date', 'Circumstances of Admission', 'Diagnosis', 'Disposition', 'Release Type', 'Release Date', 'Disposition Location'],
                'rows' => $this->getReleases(),
            ]));

        $sheets['stillPending'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Still Pending',
                'headings' => ['Case Number', 'Common Name', 'Band #', 'Age', 'Sex', 'Address Found', 'District Found', 'Admitted By',  'Admit Date', 'Circumstances of Admission', 'Diagnosis'],
                'rows' => $this->getStillPending(),
            ]));

        return $sheets;
    }

    private function getAcquisitionTotals()
    {
        return $this->scopeAcquisitionTotals()
            ->dateRange($this->start, $this->end)
            ->get()
            ->map(function ($dispositions) {
                return new Collection([
                    $dispositions->common_name,
                    $dispositions->total,
                    $dispositions->released,
                    $dispositions->transferred,
                    $dispositions->pending,
                    $dispositions->euthanized_after_24,
                    $dispositions->euthanized_in_24,
                    $dispositions->died_after_24,
                    $dispositions->died_in_24,
                    $dispositions->doa,
                ]);
            });
    }

    private function getIntakes()
    {
        return $this->scope(true)
            ->dateRange($this->start, $this->end)
            ->where('disposition', '!=', 'Void')
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->caseNumber,
                    $admission->common_name,
                    $admission->band,
                    $admission->patient->intakeExam->age_unit,
                    $admission->patient->intakeExam->sex,
                    $admission->patient->location_found,
                    $admission->patient->subdivision_found,
                    $admission->patient->admitted_by,
                    $admission->patient->admitted_at,
                    '',
                    $admission->patient->diagnosis,
                    $admission->patient->disposition,
                    $admission->patient->dispositioned_at,
                    // format_date($admission->admitted_at, config('wrmd.date_format')),
                    $admission->disposition_location,
                ]);
            });
    }

    private function getDeaths()
    {
        return $this->scope()
            ->dateRange($this->start, $this->end, 'dispositioned_at')
            ->whereDate('admitted_at', '<', $this->start)
            ->whereIn('disposition', ['Dead on arrival', 'Died +24hr', 'Died in 24hr', 'Euthanized +24hr', 'Euthanized in 24hr'])
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->caseNumber,
                    $admission->common_name,
                    $admission->band,
                    $admission->patient->intakeExam->age_unit,
                    $admission->patient->intakeExam->sex,
                    $admission->patient->location_found,
                    $admission->patient->subdivision_found,
                    $admission->patient->admitted_by,
                    $admission->patient->admitted_at,
                    '',
                    $admission->patient->diagnosis,
                    $admission->patient->disposition,
                    $admission->patient->dispositioned_at,
                ]);
            });
    }

    private function getTransfers()
    {
        return $this->scope()
            ->dateRange($this->start, $this->end, 'dispositioned_at')
            ->whereDate('admitted_at', '<', $this->start)
            ->where('disposition', 'Transferred')
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->caseNumber,
                    $admission->common_name,
                    $admission->band,
                    $admission->patient->intakeExam->age_unit,
                    $admission->patient->intakeExam->sex,
                    $admission->patient->location_found,
                    $admission->patient->subdivision_found,
                    $admission->patient->admitted_by,
                    $admission->patient->admitted_at,
                    '',
                    $admission->patient->diagnosis,
                    $admission->patient->disposition,
                    $admission->patient->transfer_type,
                    $admission->patient->dispositioned_at,
                    $admission->disposition_location,
                ]);
            });
    }

    private function getReleases()
    {
        return $this->scope()
            ->dateRange($this->start, $this->end, 'dispositioned_at')
            ->whereDate('admitted_at', '<', $this->start)
            ->where('disposition', 'Released')
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->caseNumber,
                    $admission->common_name,
                    $admission->band,
                    $admission->patient->intakeExam->age_unit,
                    $admission->patient->intakeExam->sex,
                    $admission->patient->location_found,
                    $admission->patient->subdivision_found,
                    $admission->patient->admitted_by,
                    $admission->patient->admitted_at,
                    '',
                    $admission->patient->diagnosis,
                    $admission->patient->disposition,
                    $admission->patient->release_type,
                    $admission->patient->dispositioned_at,
                    $admission->disposition_location,
                ]);
            });
    }

    private function getStillPending()
    {
        return $this->scope()
            ->whereDate('admitted_at', '<=', $this->start)
            ->where(function ($query) {
                $query->whereNull('dispositioned_at')->where('disposition', 'Pending')
                    ->orWhere(function ($query) {
                        $query->whereDate('dispositioned_at', '>=', $this->start)->where('disposition', '!=', 'Pending');
                    });
            })
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->caseNumber,
                    $admission->common_name,
                    $admission->band,
                    $admission->patient->intakeExam->age_unit,
                    $admission->patient->intakeExam->sex,
                    $admission->patient->location_found,
                    $admission->patient->subdivision_found,
                    $admission->patient->admitted_by,
                    $admission->patient->admitted_at,
                    '',
                    $admission->patient->diagnosis,
                ]);
            });
    }

    private function scope($limitToThisYear = false)
    {
        $year = $limitToThisYear ? $this->year : null;

        return Admission::owner($this->team->id, $year)
            ->select('*')
            ->joinPatients()
            ->addSelect([
                'admissions.id as id',
                'admissions.account_id as account_id',
                'admissions.patient_id as patient_id',
            ])
            ->with('patient.exams');
    }
}
