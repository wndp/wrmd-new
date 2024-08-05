<?php

namespace App\Analytics;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Series extends Collection
{
    protected $categories = [];

    public function usingCategories($categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  mixed  $items
     */
    public function localMerge($items): self
    {
        $this->items = array_merge($this->items, $this->getArrayableItems($items));

        return $this;
    }

    /**
     * Push a value onto the end of a series data array with the given name.
     */
    public function pushOnToSeriesData(string $name, int $value): self
    {
        $this->items = $this->map(function ($series) use ($name, $value) {
            if ($series['name'] === $name) {
                //$series['data'][] = $value;
                array_push($series['data'], $value);
            }

            return $series;
        })
            ->all();

        return $this;
    }

    public function trimEmptyCategories()
    {
        $rejectKeys = [];

        if ($this->isEmpty()) {
            return $rejectKeys;
        }

        foreach ($this->first()['data'] as $key => $value) {
            if (intval($this->sum("data.$key")) === 0) {
                array_push($rejectKeys, $key);
            }
        }

        $this->items = $this->map(function ($series) use ($rejectKeys) {
            $data = $series['data'];
            Arr::forget($data, $rejectKeys);
            $series['data'] = array_values($data);

            return $series;
        })
            ->toArray();

        return $rejectKeys;
    }

    public function addSeries($name, Collection $collection, $merge = []): self
    {
        if ($collection->isEmpty()) {
            return $this;
        }

        $this->localMerge([array_merge($merge, [
            'name' => $name,
            'data' => $collection->pluck('y')->toArray(),
        ])]);

        return $this;
    }

    /**
     * Add data to the series collection.
     *
     * @param \Illuminate\Support\Collection
     */
    // public function addSeriesData($seriesName, Collection $data, $valueExtractor = null)
    // {
    //     if ($data->isEmpty()) {
    //         return $this;
    //     }

    //     // if ($data instanceof ChronologicalCollection) {

    //     // }

    //     $data->pluck('y')->dd();

    //     $seriesData = tap(new BuildSeries($data, $this->categories), function ($builder) use ($valueExtractor) {
    //         $builder->makeEmptySeries()
    //             ->zeroFillSeriesArrays()
    //             ->valueFillSeriesArrays($valueExtractor);
    //     })
    //     ->getSeries();

    //     $this->localMerge($seriesData);

    //     return $this;
    // }
    // public function addSeriesData(Collection $data, $valueExtractor = null)
    // {
    //     if ($data->isEmpty()) {
    //         return $this;
    //     }

    //     $seriesData = tap(new BuildSeries($data, $this->categories), function ($builder) use ($valueExtractor) {
    //         $builder->makeEmptySeries()
    //             ->zeroFillSeriesArrays()
    //             ->valueFillSeriesArrays($valueExtractor);
    //     })
    //     ->getSeries();

    //     $this->localMerge($seriesData);

    //     return $this;
    // }

    /**
     * Add comparative data to the series collection.
     *
     * @param Collection
     */
    // public function addCompareSeriesData(Collection $data)
    // {
    //     if ($data->isEmpty()) {
    //         return $this;
    //     }

    //     $seriesData = tap(new BuildSeries($data, $this->categories), function ($builder) {
    //         $builder->makeEmptySeries()
    //             ->zeroFillSeriesArrays()
    //             ->valueFillCompareSeriesArrays();
    //     })
    //     ->getSeries();

    //     $this->localMerge($seriesData);

    //     return $this;
    // }

    public function addPieSeriesData(Collection $data): self
    {
        if ($data->isEmpty()) {
            return $this;
        }

        if ($this->isNotEmpty()) {
            $data = $this->first()['data']->pluck('y', 'name')->merge($data);
            $this->items = [];
        }

        $seriesData = tap(new PieSeries($data), function ($series) {
            $series->valueFillSeriesArrays();
        })->getSeries();

        $this->localMerge($seriesData);

        return $this;
    }

    public function addTreeMapSeriesData(Collection $data): self
    {
        if ($data->isEmpty()) {
            return $this;
        }

        $seriesData = tap(new TreeMapSeries($data, $this->categories), function ($series) {
            $series->setTreeMapSeries();
        })->getSeries();

        $this->localMerge($seriesData);

        return $this;
    }
}
