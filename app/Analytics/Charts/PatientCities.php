<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Models\Admission;
use Illuminate\Support\Collection;

class PatientCities extends Chart
{
    use HandleChronologicalData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handleChronologicalDataWithSubGroups();
    }

    /**
     * Query for the requested data.
     */
    public function query($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->addSelect('city_found as subgroup')
            ->joinPatients()
            ->whereNotNull('city_found')
            ->groupBy('date')
            ->groupBy('subgroup')
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($this->aggregateData($query->get())->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->addSelect('city_found as subgroup')
            ->joinPatients()
            ->whereNotNull('city_found')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->groupBy('date')
            ->groupBy('subgroup')
            ->limit(10)
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($this->aggregateData($query->get())->mapInto(DataSet::class));
    }

    public function aggregateData($data)
    {
        $mostPrevelent = $data->groupBy('subgroup')->map(function ($group) {
            return $group->sum('aggregate');
        })->sortByDesc(function ($sum) {
            return $sum;
        })
            ->take(15)
            ->keys();

        return $data->whereIn('subgroup', $mostPrevelent);
    }
}
