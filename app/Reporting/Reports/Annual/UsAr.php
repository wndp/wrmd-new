<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Facades\DB;

/**
 * Arkansas, United Sates.
 */
class UsAr extends AnnualReport
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
        return 'reporting.reports.annual.usAr';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Arkansas Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        return [
            'year' => $this->year,
            'heldOver' => $this->heldOver(),
        ];
    }

    public function heldOver()
    {
        return $this->scopeAcquisitionTotals()
            ->addSelect(DB::raw('year(admitted_at) as year_acquired'))
            ->where('case_year', '<', $this->year)
            ->whereRaw("(`disposition` = 'Pending' or year(`dispositioned_at`) = {$this->year})")
            ->groupBy('year_acquired', 'species_id')
            ->get();

        //       "transfer_released" => "0"
        // "transfer_continued_care" => "0"
        // "transfer_education_or_scientific_research_individual" => "0"
        // "transfer_education_or_scientific_research_institute" => "0"
        // "transfer_falconry_or_raptor_propagation" => "0"
        // "transfer_other" => "0"

        //$this->applyFilters();

        // return Admission::where('team_id', $this->team->id)
        //     //->select(['common_name', 'admitted_at', 'diagnosis', 'disposition', 'dispositioned_at'])
        //     ->joinPatients()
        //     // ->join('species', 'patients.species_id', '=', 'species.id')
        //     // ->where([
        //     //     'class' => 'Aves',
        //     //     'is_mbta' => '1',
        //     // ])
        //     ->where('case_year', '<', $this->year)
        //     ->where('disposition', '!=', 'Void')
        //     ->whereRaw("(`disposition` = 'Pending' or year(`dispositioned_at`) = {$this->year})")
        //     ->orderBy('common_name');

        // $admissions = Admission::owner($this->team->id, $this->year)
        //     ->select('admissions.*')
        //     ->joinPatients()
        //     ->where('disposition', '!=', 'Void')
        //     ->with('patient')
        //     ->get();
    }
}
