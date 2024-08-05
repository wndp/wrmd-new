<?php

namespace App\Analytics;

use Illuminate\Support\Collection;

class PieSeries
{
    protected $data;

    protected $series;

    protected $seriesName;

    public function __construct(Collection $data, $seriesName = '')
    {
        $this->data = $data;
        $this->seriesName = $seriesName;
        $this->series = new Collection();
    }

    public function valueFillSeriesArrays()
    {
        $this->series->push([
            'name' => $this->seriesName,
            'data' => $this->data->map(function ($aggregate, $name) {
                return [
                    'name' => $name,
                    'y' => $aggregate,
                ];
            })->values(),
        ]);

        return $this;
    }

    public function getSeries()
    {
        return $this->series;
    }

    public function merge(self $series)
    {
        $this->series = $this->series->merge($series->getSeries());
    }

    // public function jsonSerialize()
    // {
    //     return $this->series->sortBy('data.name')->values();
    // }
}
