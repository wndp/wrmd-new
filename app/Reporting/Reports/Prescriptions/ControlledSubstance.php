<?php

namespace App\Reporting\Reports\Prescriptions;

use App\Models\Prescription;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ControlledSubstance extends Report
{
    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Controlled Substance Log');
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.prescriptions.controlled';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect((new DateRange('rx_started_at'))->toArray());
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $query = Prescription::select('prescriptions.*')
            ->with('patient.admissions')
            ->join('patients', 'prescriptions.patient_id', '=', 'patients.id')
            ->join('admissions', 'patients.id', '=', 'admissions.patient_id')
            ->whereNull('voided_at')
            ->where('team_id', $this->team->id)
            ->where('is_controlled_substance', true);

        $controlled = $this->applyFilters($query)
            ->get()
            ->flatMap(function ($prescription) {
                return $prescription->administrations;
            })
            ->sortBy(function ($administration) {
                return $administration->administered_at->timestamp;
            });

        $dateFrom = Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format'));
        $dateTo = Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format'));

        return compact('controlled', 'dateFrom', 'dateTo');
    }
}
