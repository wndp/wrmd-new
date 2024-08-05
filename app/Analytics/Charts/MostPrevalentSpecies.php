<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;

class MostPrevalentSpecies extends Chart
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
    public function query($segment): ChronologicalCollection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->groupBy('date')
            ->orderBy('date')
            ->addSelect('common_name as subgroup')
            ->groupBy('subgroup');

        $this->withSegment($query, $segment);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $data = $query->get()->groupBy('subgroup')->sortByDesc(function ($collection) {
            return $collection->sum('aggregate');
        })->take(5)->flatten();

        return new ChronologicalCollection($data->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): ChronologicalCollection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->groupBy('date')
            ->orderBy('date')
            ->addSelect('common_name as subgroup')
            ->groupBy('subgroup');

        $this->withSegment($query, $segment);

        $data = $query->get()->groupBy('subgroup')->sortByDesc(function ($collection) {
            return $collection->sum('aggregate');
        })->take(5)->flatten();

        return new ChronologicalCollection($data->mapInto(DataSet::class));
    }
}
