<?php

namespace App\Reporting\Reports\Annual;

use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;

/**
 * Massachusetts, United Sates
 */
class UsMa extends AnnualReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.annual.usMa';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'Massachusetts Wildlife Rehabilitation Annual Report';
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
            ->with('patient.species')
            ->orderBy('admitted_at')
            ->get();

        return [
            'year' => $this->year,
            'collection' => $admissions->groupBy([function ($admission) {
                return $admission->patient->species->class;
            }, function ($admission) {
                return $admission->patient->taxon_id;
            }, function ($admission) {
                return format_disposition($admission->patient->disposition);
            }])
                ->sortKeys()
                // Sort taxa collections by common name
                ->map(function ($classCollection) {
                    return $classCollection->sortBy(function ($taxaCollection) {
                        return $taxaCollection->first()->first()->patient->common_name;
                    });
                }),
        ];
    }
}
