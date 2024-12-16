<?php

namespace App\Reporting\Reports\Location;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Listing\ListingQuery;
use App\Models\Admission;
use App\Patients\PatientLocation;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\LocationArea;
use App\Reporting\Filters\WhereHolding;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PendingPatientsByLocation extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.location.pending';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Pending Patients by Location');
    }

    public function filters(): Collection
    {
        return parent::filters()->merge(array_merge((new DateRange('moved_in_at'))->toArray(), [
            new WhereHolding,
            new LocationArea,
        ]));
    }

    /**
     * Get pending cases in the clinic by location.
     */
    public function data(): array
    {
        $cases = $this->applyFilters($this->scopePatientsInLocations())->get();

        return [
            'locations' => $this->groupAndSortLocations($cases),
            'whereHolding' => $this->getAppliedFilterValue(WhereHolding::class),
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
        ];
    }

    /**
     * Query all pending patients who are in an enclosure.
     */
    protected function scopePatientsInLocations(): Builder
    {
        [$pendingPatientId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value,
        ]);

        return ListingQuery::run()
            ->selectAdmissionKeys()
            ->joinPatientLocations()
            ->addSelect(['patient_locations.id as location_id'])
            ->where('team_id', $this->team->id)
            ->whereNotNull('area')
            ->where('disposition_id', $pendingPatientId)
            ->with('patient.locations');

        // return Admission::where('team_id', $this->team->id)
        //     ->selectAdmissionKeys()
        //     ->joinPatients()
        //     ->joinLastLocation()
    }

    /**
     * Group and sort the patients by their enclosure.
     */
    protected function groupAndSortLocations(Collection $cases): Collection
    {
        return $cases->groupBy(function ($accountPatient) {
            $location = $accountPatient
                ->patient
                ->locations
                ->where('id', $accountPatient->location_id)
                ->first();

            return $location instanceof PatientLocation ? $location->location : null;
        })
            ->sortBy(function ($value, $key) {
                return $key;
            });
    }
}
