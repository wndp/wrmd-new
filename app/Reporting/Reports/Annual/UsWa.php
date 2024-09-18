<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Washington, United Sates
 */
class UsWa extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usWa';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Washington Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        return [
            'year' => $this->year,
            'counts' => $this->counts(),
            'heldOver' => $this->heldOver(),
            'threatened' => $this->threatened(),
            'education' => $this->education(),
            'nonReleasable' => $this->nonReleasable(),
        ];
    }

    /**
     * Total numbers for various dispositions.
     */
    private function counts()
    {
        return Admission::owner($this->team->id, $this->year)
            ->addSelect(DB::raw("sum(if(`class` = 'Mammalia' and `disposition` != 'Dead on arrival', 1, 0)) AS `mammals`"))
            ->addSelect(DB::raw("sum(if(`class` = 'Aves' and `disposition` != 'Dead on arrival', 1, 0)) AS `birds`"))
            ->addSelect(DB::raw("sum(if((`class` = 'Reptilia' OR `class` = 'Amphibia') and `disposition` != 'Dead on arrival', 1, 0)) AS `herps`"))
            ->addSelect(DB::raw("sum(if(`disposition` != 'Dead on arrival', 1, 0)) AS `total`"))
            ->addSelect(DB::raw("sum(if(`disposition` = 'Released', 1, 0)) AS `released`"))
            ->addSelect(DB::raw("sum(if(`disposition` = 'Transferred', 1, 0)) AS `transferred`"))
            ->joinPatients()
            ->joinTaxa()
            ->where('disposition', '!=', 'Void')
            ->get()
            ->first();
    }

    /**
     * Number of animals in possession held over from last year.
     */
    private function heldOver(): Collection
    {
        return Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->where('case_year', '<', $this->year)
            ->where('disposition', '!=', 'Void')
            ->whereRaw("(`disposition` = 'Pending' or year(`dispositioned_at`) = {$this->year})")
            ->count();
    }

    /**
     * Threatened or Endangered Species treated.
     */
    private function threatened(): Collection
    {
        // Excluding bald eagles and falcons
        return new \Illuminate\Support\Collection();
    }

    /**
     * Non-releasable animals held in your possession for education.
     */
    private function education(): Collection
    {
        // No way to know if animal was transfered into "your possession for education".
        return new \Illuminate\Support\Collection();
    }

    /**
     * Non-releasable animals held in possession for orphan imprinting or other behavioral rehabilitation.
     */
    private function nonReleasable(): Collection
    {
        return new \Illuminate\Support\Collection();
    }
}
