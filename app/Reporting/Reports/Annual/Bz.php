<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Filters\MonthFilter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Belize.
 */
class Bz extends AnnualReport implements WithMultipleSheets
{
    public $canExport = true;

    protected $month;

    protected $lastOfMonth;

    protected $firstOfMonth;

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Belize Wildlife Rehabilitation Monthly Report';
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.bz';
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
            new MonthFilter()
        );
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        return array_merge($this->sheets(), [
            'year' => $this->year,
            'month' => Carbon::createFromFormat('!m', $this->month)->format('F'),
        ]);
    }

    public function sheets(): array
    {
        parent::data();

        $this->month = $this->getAppliedFilterValue(MonthFilter::class);
        $this->lastOfMonth = Carbon::createFromDate($this->year, $this->month, 1)->lastOfMonth();
        $this->firstOfMonth = Carbon::createFromDate($this->year, $this->month, 1)->firstOfMonth();

        $sheets['intake'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Intake',
                'headings' => ['Name', 'Common Name', 'Band Number', 'Address Found', 'District Found', 'Intake Date', 'Disposition', 'WRMD #'],
                'rows' => $this->getIntakes(),
            ]));

        $sheets['transfers'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Transferred Out',
                'headings' => ['Name', 'Common Name', 'Band Number', 'Address Found', 'District Found', 'Transfer Date', 'Transfer To', 'WRMD #'],
                'rows' => $this->getTransfers(),
            ]));

        $sheets['deaths'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Deaths',
                'headings' => ['Name', 'Common Name', 'Band Number', 'Address Found', 'District Found', 'Admit Date', 'Disposition', 'Disposition Date', 'WRMD #'],
                'rows' => $this->getDeaths(),
            ]));

        $sheets['releases'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Releases',
                'headings' => ['Name', 'Common Name', 'Band Number', 'Address Found', 'District Found', 'Release Date', 'Release Location', 'WRMD #'],
                'rows' => $this->getReleases(),
            ]));

        $sheets['inventory'] = (new AnnualReportSheet($this->team))
            ->withFilters($this->appliedFilters)
            ->withRequest(new Request([
                'title' => 'Inventory',
                'headings' => ['Name', 'Common Name', 'Band Number', 'Address Found', 'District Found', 'Intake Date', 'WRMD #'],
                'rows' => $this->getInventory(),
            ]));

        return $sheets;
    }

    private function getIntakes()
    {
        return $this->scope(true)
            ->dateRange($this->firstOfMonth->format('Y-m-d'), $this->lastOfMonth->format('Y-m-d'))
            ->where('disposition', '!=', 'Void')
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->name,
                    $admission->common_name,
                    $admission->band,
                    $admission->address_found,
                    $admission->subdivision_found,
                    format_date($admission->admitted_at, config('wrmd.date_format')),
                    $admission->disposition,
                    $admission->caseNumber,
                ]);
            });
    }

    private function getReleases()
    {
        return $this->scope()
            ->dateRange($this->firstOfMonth->format('Y-m-d'), $this->lastOfMonth->format('Y-m-d'), 'dispositioned_at')
            ->where('disposition', 'Released')
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->name,
                    $admission->common_name,
                    $admission->band,
                    $admission->address_found,
                    $admission->subdivision_found,
                    format_date($admission->dispositioned_at, config('wrmd.date_format')),
                    $admission->disposition_address,
                    $admission->caseNumber,
                ]);
            });
    }

    private function getTransfers()
    {
        return $this->scope()
            ->dateRange($this->firstOfMonth->format('Y-m-d'), $this->lastOfMonth->format('Y-m-d'), 'dispositioned_at')
            ->where('disposition', 'Transferred')
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->name,
                    $admission->common_name,
                    $admission->band,
                    $admission->address_found,
                    $admission->subdivision_found,
                    format_date($admission->dispositioned_at, config('wrmd.date_format')),
                    $admission->disposition_address,
                    $admission->caseNumber,
                ]);
            });
    }

    private function getDeaths()
    {
        return $this->scope()
            ->dateRange($this->firstOfMonth->format('Y-m-d'), $this->lastOfMonth->format('Y-m-d'), 'dispositioned_at')
            ->whereIn('disposition', ['Dead on arrival', 'Died +24hr', 'Died in 24hr', 'Euthanized +24hr', 'Euthanized in 24hr'])
            ->get()
            ->map(function ($admission) {
                return new Collection([
                    $admission->name,
                    $admission->common_name,
                    $admission->band,
                    $admission->address_found,
                    $admission->subdivision_found,
                    format_date($admission->admitted_at, config('wrmd.date_format')),
                    $admission->disposition,
                    format_date($admission->dispositioned_at, config('wrmd.date_format')),
                    $admission->caseNumber,
                ]);
            });
    }

    private function getInventory()
    {
        return $this->scope()
            ->whereDate('admitted_at', '<=', $this->lastOfMonth->format('Y-m-d'))
            ->where(function ($query) {
                $query->whereNull('dispositioned_at')->where('disposition', 'Pending')
                    ->orWhere(function ($query) {
                        $query->whereDate('dispositioned_at', '>=', $this->lastOfMonth->format('Y-m-d'))->where('disposition', '!=', 'Pending');
                    });
            })
            ->get()
            ->groupBy(function ($accountPatient) {
                return $accountPatient->patient->currentLocation;
            })
            ->map(function ($collection, $location) {
                return $collection->map(function ($admission) {
                    return [
                        $admission->name,
                        $admission->common_name,
                        $admission->band,
                        $admission->address_found,
                        $admission->subdivision_found,
                        format_date($admission->admitted_at, config('wrmd.date_format')),
                        $admission->caseNumber,
                    ];
                });
            });
    }

    private function scope($limitToThisYear = false)
    {
        $year = $limitToThisYear ? $this->year : null;

        return Admission::owner($this->team->id, $year)
            ->select('*')
            ->joinPatients()
            ->addSelect([
                'admissions.case_id as id',
                'admissions.account_id as account_id',
                'admissions.patient_id as patient_id',
            ])
            ->with('patient');
    }
}
