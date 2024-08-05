<?php

namespace App\Analytics;

use Illuminate\Support\Collection;

class AreaRangeCollection extends Collection
{
    public function aggregateData()
    {
        $data = $this->groupBy('date')
            ->map(function ($collection, $date) {
                return new DataSet([
                    'date' => $date,
                    'aggregates' => $collection->pluck('aggregate'),
                ]);
            })
            ->values();

        return new ChronologicalCollection($data);
    }

    public function rangeFromAggregate($data)
    {
        $aggregates = $data->pluck('aggregates')->filter()->flatten();

        return [$data[0]->date, (int) $aggregates->min(), (int) $aggregates->max()];
    }

    public function averageFromAggregate($data)
    {
        $aggregates = $data->pluck('aggregates')->filter()->flatten();

        return [$data[0]->date, round($aggregates->average(), 2)];
    }
}
