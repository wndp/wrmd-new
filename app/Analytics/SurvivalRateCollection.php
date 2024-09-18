<?php

namespace App\Analytics;

use App\Analytics\Concerns\HandleAggregates;
use App\Analytics\Concerns\HandleSeriesNames;
use Illuminate\Support\Number;

class SurvivalRateCollection extends CategorizedCollection
{
    use HandleAggregates;
    use HandleSeriesNames;

    public function calculateSurvivalRates()
    {
        $this->items = $this->map(function ($dataSet) {
            $dataSet->offsetSet('including24Hours', Number::survivalRate($dataSet, false));
            $dataSet->offsetSet('after24Hours', Number::survivalRate($dataSet, true));

            return $dataSet;
        })
            ->all();

        return $this;
    }

    public function includingFirst24Hrs(callable $callback)
    {
        $data = $this->map(function ($dataset) {
            return [
                'name' => $dataset['subgroup'],
                'y' => $dataset['including24Hours'],
            ];
        });

        $callback($data);

        return $this;
    }

    public function afterFirst24Hrs(callable $callback)
    {
        $data = $this->map(function ($dataset) {
            return [
                'name' => $dataset['subgroup'],
                'y' => $dataset['after24Hours'],
            ];
        });

        $callback($data);

        return $this;
    }
}
