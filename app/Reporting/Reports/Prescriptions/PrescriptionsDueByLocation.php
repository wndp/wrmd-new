<?php

namespace App\Reporting\Reports\Prescriptions;

use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateOn;
use App\Reporting\Filters\LocationArea;
use App\Reporting\Filters\WhereHolding;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PrescriptionsDueByLocation extends Report
{
    use ReportUtilities;

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Prescriptions Due By Location');
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.prescriptions.due-by-location';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect([
            new DateOn(null, 'rx_started_at'),
            new WhereHolding,
            new LocationArea,
        ]);
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $date = Carbon::parse($this->getAppliedFilterValue(DateOn::class));

        $locations = $this->applyFilters(
            $this->scopePrescriptions($date->format('Y-m-d')),
            [DateOn::class]
        )
            //->joinLastLocation()
            ->leftJoinCurrentLocation()
            ->get()
            ->filter(function ($prescription) use ($date) {
                return $prescription->isDueOn($date->format('Y-m-d'));
            })
            ->groupBy(function ($prescription) {
                return isset($prescription->patient->locations[0]) ? $prescription->patient->locations[0]->location : '';
            })
            ->sortBy(function ($value, $key) {
                return $key;
            })
            ->map(function ($prescriptions) {
                return $prescriptions->groupBy(function ($prescription) {
                    return $this->groupByCaseNumber($prescription);
                });
            });

        return [
            'date' => $date->format(config('wrmd.date_format')),
            'whereHolding' => $this->getAppliedFilterValue(WhereHolding::class),
            'locations' => $locations,
        ];
    }
}
