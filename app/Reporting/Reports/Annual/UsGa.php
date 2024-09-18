<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Collection;

/**
 * Georgia, United Sates
 */
class UsGa extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usGa';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Georgia Wildlife Rehabilitation Annual Report';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        return [
            'year' => $this->year,
            'admissions' => $this->getAdmissions(),
            'rabiesVectors' => $this->rabiesVectors(),
            'nonRabiesVectors' => $this->nonRabiesVectors(),
            'bats' => $this->bats(),
            'deer' => $this->deer(),
            'raptors' => $this->raptors(),
            'nonRaptors' => $this->nonRaptors(),
            'herpitiles' => $this->herpitiles(),
        ];
    }

    /**
     * Get the accounts cases.
     */
    private function getAdmissions(): Collection
    {
        return Admission::owner($this->team->id, $this->year)
            ->select('admissions.*')
            ->joinPatients()
            ->where('disposition', '!=', 'Void')
            ->with('patient.rescuer', 'patient.exams')
            ->get();
    }

    /**
     * Rabies vector species.
     */
    protected function rabiesVectors(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Mammalia', true))
            ->where('common_name', 'regexp', 'raccoon|skunk|fox|bobcat')
            ->get();
    }

    /**
     * Non-rabies vector species.
     */
    protected function nonRabiesVectors(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Mammalia'))
            ->where('common_name', 'not regexp', 'raccoon|skunk|fox|bobcat')
            ->get();
    }

    /**
     * Bats.
     */
    protected function bats(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Mammalia'))
            ->where('common_name', 'like', '%bat%')
            ->get();
    }

    /**
     * Deer.
     */
    protected function deer(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Mammalia'))
            ->where('common_name', 'like', '%deer%')
            ->get();
    }

    /**
     * Raptors.
     */
    protected function raptors(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Aves'))
            ->where('lay_groups', 'like', '%raptor%')
            ->get();
    }

    /**
     * Non-raptors.
     */
    protected function nonRaptors(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals('Aves'))
            ->where('lay_groups', 'not like', '%raptor%')
            ->get();
    }

    /**
     * Herpitiles.
     */
    protected function herpitiles(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals())
            ->whereIn('class', ['reptilia', 'amphibia'])
            ->get();
    }
}
