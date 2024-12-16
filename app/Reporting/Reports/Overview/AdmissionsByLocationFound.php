<?php

namespace App\Reporting\Reports\Overview;

use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\LocationFoundFilter;
use App\Reporting\Filters\SpeciesGrouping;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdmissionsByLocationFound extends Report
{
    /**
     * {@inheritdoc}
     */
    public function title(): string
    {
        return __('Patients by Location Found');
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return 'The Admissions by Location Found report give you 3 filters to view the total number of species that came into care by the city, county or state they were found in.';
    }

    /**
     * {@inheritdoc}
     */
    public function viewPath(): string
    {
        return 'reports.overview.admissions-by-location-found';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect(array_merge((new DateRange)->toArray(), [
            new LocationFoundFilter,
            new SpeciesGrouping,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    protected function data(): array
    {
        $locationOptions = (new LocationFoundFilter)->options();

        $query = Admission::where('team_id', $this->team->id)
            ->select('common_name', 'taxon_id', ...array_keys($locationOptions))
            ->joinPatients()
            ->orderBy('common_name');

        $admissions = $this->applyFilters($query)
            ->get()
            ->groupBy(function ($admission) {
                return $admission->{$this->getAppliedFilterValue(LocationFoundFilter::class)};
            })
            ->sortKeys()
            ->map(function ($collection) {
                return $collection->groupBy($this->getAppliedFilterValue(SpeciesGrouping::class))
                    ->keyBy(function ($collection) {
                        return $collection->first()->common_name;
                    });
            });

        return [
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
            'groupedBy' => $locationOptions[$this->getAppliedFilterValue(LocationFoundFilter::class)],
            'admissions' => $admissions,
        ];
    }
}
