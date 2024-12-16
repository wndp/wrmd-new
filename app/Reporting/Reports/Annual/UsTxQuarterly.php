<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\QuarterYear;
use App\Reporting\Filters\YearFilter;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Texas, United Sates.
 */
class UsTxQuarterly extends AnnualReport
{
    public $canExport = true;

    protected $quarters;

    protected $whichQuarter;

    /**
     * Get the report format options.
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
        return 'reports.spreadsheet';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Texas Wildlife Rehabilitation Quarterly Report';
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return 'The Texas Parks and Wildlife Department requires you to submit their official Wildlife Rehabilitation Permit Quarterly Report. You may use this report to help generate data for that report. You should export this report and copy it\'s data into the official Texas Parks Wildlife Rehabilitation Permit Quarterly Report. If you transfer patients within WRMD from one account to another, we fill in the "Facility Number" fields with the name of the organization you transfered with. You will need to replace the organization name with their TPWD facility number.';
    }

    /**
     * Get the reports filters.
     */
    public function filters(): Collection
    {
        return parent::filters()->push(
            new QuarterYear
        );
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $headings = $this->headings();
        $data = $this->query()->get()->map(function ($row) {
            return $this->map($row);
        });

        return [
            'year' => $this->year,
            'dateFrom' => Carbon::parse($this->quarters[$this->whichQuarter][0])->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->quarters[$this->whichQuarter][1])->format(config('wrmd.date_format')),
            'headings' => $headings,
            'data' => $data,
        ];
    }

    public function dispositionMethod($patient)
    {
        if (Str::contains($patient->disposition, 'Died')) {
            return 'Death';
        } elseif (Str::contains($patient->disposition, 'Euthanized')) {
            return 'Euthanasia';
        } elseif ($patient->disposition === 'Transferred') {
            return 'Transfer Out';
        } elseif ($patient->disposition === 'Released' && $patient->release_type !== 'Self') {
            return 'Release';
        } elseif ($patient->disposition === 'Released' && $patient->release_type === 'Self') {
            return 'Escaped';
        }
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return [
            'Species',
            'Unique Identification',
            'Age',
            'Sex',
            'Intake Date',
            'Intake Method',
            'Collection County',
            'Collection Latitude',
            'Collection Longitude',
            'Collection Address',
            'Facility Number of Origin',
            'Unique Identification2',
            'Disposition Date',
            'Disposition Method',
            'Release County',
            'Release Latitude',
            'Release Longitude',
            'Release Address',
            'Facility Number Receiving',
        ];
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        $transfersFrom = $row->patient->transfers->where('to_account_id', $this->team->id);
        $transfersTo = $row->patient->transfers->where('from_account_id', $this->team->id);

        return [
            $row->patient->common_name,
            $row->case_number,
            $row->patient->intakeExam->age_unit,
            $row->patient->intakeExam->sex,
            $row->patient->admitted_at->format('m/d/Y'),
            $transfersFrom->isEmpty() ? 'Public Turn-in' : 'Transfer In',
            $row->patient->county_found,
            $row->patient->lat_found,
            $row->patient->lng_found,
            $row->patient->location_found,
            $transfersFrom->isNotEmpty() ? $transfersFrom->first()->fromAccount->organization : '',
            $transfersFrom->isNotEmpty() ? $transfersFrom->first()->fromAccountAdmission()->case_number : '',
            $row->patient->dispositioned_at?->format('m/d/Y'),
            $this->dispositionMethod($row->patient),
            $row->patient->disposition === 'Released' ? $row->patient->disposition_county : '',
            $row->patient->disposition === 'Released' ? $row->patient->disposition_lat : '',
            $row->patient->disposition === 'Released' ? $row->patient->disposition_lng : '',
            $row->patient->disposition === 'Released' ? $row->patient->disposition_locale : '',
            $transfersTo->isNotEmpty() ? $transfersTo->first()->toAccount->organization : '',
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        $this->year = intval($this->getAppliedFilterValue(YearFilter::class));
        $this->whichQuarter = intval($this->getAppliedFilterValue(QuarterYear::class));

        $this->quarters = [
            '1' => [$this->year.'-01-01', $this->year.'-03-31'],
            '2' => [$this->year.'-04-01', $this->year.'-06-30'],
            '3' => [$this->year.'-07-01', $this->year.'-09-30'],
            '4' => [$this->year.'-10-01', $this->year.'-12-31'],
        ];

        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->where(function ($query) {
                $start = $this->quarters[$this->whichQuarter][0];
                $end = $this->quarters[$this->whichQuarter][1];
                $query->whereRaw("admitted_at between '$start' and '$end 23:59:59.999999'")
                    ->orWhereRaw("dispositioned_at between '$start' and '$end 23:59:59.999999'");
            })
            ->orderBy('case_year')
            ->orderBy('admissions.case_id')
            ->with('patient.exams', 'patient.transfers');
    }
}
