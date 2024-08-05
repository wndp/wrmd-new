<?php

namespace App\Analytics\Concerns;

trait HandlePieData
{
    use HandleSeriesNames;

    public function handlePieData()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series->addPieSeriesData(
                $this->query($segment)
                    ->pluck('aggregate', 'subgroup')
                    ->sortKeys()
                    ->mapWithKeys(function ($value, $name) use ($segment) {
                        $name = $this->formatSeriesName($segment, $name, $this->filters->compare, $this->filters->date_from, $this->filters->date_to);

                        return [$name => $value];
                    })
            );

            if ($this->filters->compare) {
                $this->series->addPieSeriesData(
                    $this->compareQuery($segment)
                        ->pluck('aggregate', 'subgroup')
                        ->sortKeys()
                        ->mapWithKeys(function ($value, $name) use ($segment) {
                            $name = $this->formatSeriesName($segment, $name, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to);

                            return [$name => $value];
                        })
                );
            }
        }
    }
}
