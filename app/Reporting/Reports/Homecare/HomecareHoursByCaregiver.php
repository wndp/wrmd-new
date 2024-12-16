<?php

namespace App\Reporting\Reports\Homecare;

use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\YearFilter;
use Illuminate\Support\Collection;

class HomecareHoursByCaregiver extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.homecare.total';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Homecare Hours by Caregiver');
    }

    public function filters(): Collection
    {
        return parent::filters()->merge([
            new YearFilter(Admission::yearsInAccount($this->team->id)),
        ]);
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $year = $this->getAppliedFilterValue(YearFilter::class);

        $caregivers = Admission::where([
            'team_id' => $this->team->id,
            'case_year' => $year,
        ])
            ->select('area', 'enclosure')
            //->selectRaw('sum(hours) as hours_sum')
            ->joinPatients()
            ->join('patient_locations', 'patients.id', '=', 'patient_locations.patient_id')
            ->where('facility', 'homecare')
            ->whereNull('patient_locations.deleted_at')
            //->groupBy('area')
            //->orderByDesc('hours_sum')
            ->get()
            ->countBy('area')
            ->sort();

        return [
            'caseYear' => $year,
            'caregivers' => $caregivers,
        ];
    }
}
