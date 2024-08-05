<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;

class PatientsByTaxonomicClass extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series->addPieSeriesData(
                $this->query($segment)
                    ->pluck('aggregate', 'class')
                    ->sortKeys()
                    ->mapWithKeys(function ($value, $name) use ($segment) {
                        $name = $this->formatSeriesName($segment, $name, $this->filters->compare, $this->filters->date_from, $this->filters->date_to);

                        return [$name => $value];
                    })
            );

            if ($this->filters->compare) {
                $this->series->addPieSeriesData(
                    $this->compareQuery($segment)
                        ->pluck('aggregate', 'class')
                        ->sortKeys()
                        ->mapWithKeys(function ($value, $name) use ($segment) {
                            $name = $this->formatSeriesName($segment, $name, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to);

                            return [$name => $value];
                        })
                );
            }
        }
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, class')
            ->joinPatients()
            ->joinTaxa()
            ->orderByDesc('aggregate')
            ->groupBy('class');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, class')
            ->joinPatients()
            ->joinTaxa()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->orderByDesc('aggregate')
            ->groupBy('class');

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
