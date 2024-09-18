<?php

namespace App\Reporting\Reports\Homecare;

use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PatientsSentToHomecare extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.homecare.sent_to_homecare';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Patients Sent to Homecare');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge((new DateRange('moved_in_at'))->toArray());
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->distinct()
            ->joinPatients()
            ->join('patient_locations', 'patients.id', '=', 'patient_locations.patient_id')
            ->where('facility', 'homecare')
            ->whereNull('patient_locations.deleted_at')
            ->with(['patient.locations' => function ($query) {
                $query->where('facility', 'homecare');
            }]);

        return [
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
            'admissions' => $this->applyFilters($query)->get(),
        ];
    }
}
