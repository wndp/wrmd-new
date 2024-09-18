<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * United Sates
 */
class Us extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return 'USFWS Wildlife Rehabilitation Annual Report';
    }

    /**
     * {@inheritdoc}
     */
    public function viewPath(): string
    {
        return 'reports.annual.us';
    }

    /**
     * {@inheritdoc}
     */
    public function data(): array
    {
        parent::data();

        $a = $this->heldOver();
        $b = $this->acquisitions();
        $c = $this->reportedInjuries();
        $d = $this->stillPending();
        $e = $this->transfers();

        return array_merge($a, $b, [
            'reportedInjuries' => $c,
            'stillPendings' => $d,
            'transfers' => $e,
            'year' => $this->year,
        ]);
    }

    /**
     * Section A of US form 3-202-4.
     */
    public function heldOver(): array
    {
        $return['heldOverEagles'] = $this->scopeHeldOver()
            ->where('common_name', 'like', '%eagle%')
            ->get();

        $return['heldOverBirds'] = $this->scopeHeldOver()
            ->where('common_name', 'not like', '%eagle%')
            ->get();

        return $return;
    }

    /**
     * Section B of US form 3-202-4.
     */
    public function acquisitions(): array
    {
        $return['eagleAcquisitions'] = $this->scopeAcquisitions()
            ->where('common_name', 'like', '%eagle%')
            ->get();

        $return['birdAcquisitions'] = $this->scopeAcquisitions()
            ->where('common_name', 'not like', '%eagle%')
            ->get();

        return $return;
    }

    /**
     * Section C of US form 3-202-4.
     */
    public function reportedInjuries(): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select(['admissions.case_id', 'common_name', 'admitted_at', 'reasons_for_admission', 'diagnosis', 'disposition', 'county_found', 'subdivision_found'])
            ->joinPatients()
            ->joinTaxa()
            ->where([
                'class' => 'Aves',
                'is_mbta' => '1',
                'criminal_activity' => '1',
            ])
            ->where('disposition', '!=', 'Void')
            ->orderBy('common_name');

        return $this->applyFilters($query)->get();
    }

    /**
     * Section D of US form 3-202-4.
     * Birds that are pending on December 31 of the reporting year.
     */
    public function stillPending(): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select(['admissions.case_id', 'common_name', 'diagnosis', 'admitted_at'])
            ->joinPatients()
            ->joinTaxa()
            ->where([
                'class' => 'Aves',
                'is_mbta' => '1',
            ])
            ->where('disposition', '!=', 'Void')
            ->whereRaw("(`disposition` = 'Pending' or year(`dispositioned_at`) > {$this->year})")
            ->orderBy('common_name');

        return $this->applyFilters($query)->get();
    }

    /**
     * Section E of US form 3-202-4.
     * Birds that were transfered in the reporting year.
     */
    public function transfers(): Collection
    {
        return Admission::where('team_id', $this->team->id)
            ->select(['common_name', 'disposition_address', 'disposition_subdivision', 'dispositioned_at', 'transfer_type'])
            ->joinPatients()
            ->joinTaxa()
            ->where([
                'class' => 'Aves',
                'is_mbta' => '1',
                'disposition' => 'Transferred',
            ])
            ->whereRaw("year(`dispositioned_at`) = {$this->year}")
            ->orderBy('common_name')
            ->get();
    }

    /**
     * Common query arguments for section A of US form 3-202-4.
     */
    private function scopeHeldOver(): Builder
    {
        return Admission::where('team_id', $this->team->id)
            ->select(['common_name', 'admitted_at', 'diagnosis', 'disposition', 'dispositioned_at'])
            ->joinPatients()
            ->joinTaxa()
            ->where([
                'class' => 'Aves',
                'is_mbta' => '1',
            ])
            ->where('case_year', '<', $this->year)
            ->where('disposition', '!=', 'Void')
            ->whereRaw("(`disposition` = 'Pending' or year(`dispositioned_at`) = {$this->year})")
            ->orderBy('common_name');
    }

    /**
     * Common query arguments for section B of US form 3-202-4.
     */
    private function scopeAcquisitions(): Builder
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Aves')->where('is_mbta', '1'));
    }
}
