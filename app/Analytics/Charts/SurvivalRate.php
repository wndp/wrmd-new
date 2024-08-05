<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Analytics\SurvivalRateCollection;

class SurvivalRate extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->series->localMerge([
            [
                'name' => 'Including first 24 hours',
                'data' => [],
            ],
        ])
            ->localMerge([
                [
                    'name' => 'After first 24 hours',
                    'data' => [],
                ],
            ]);

        foreach ($this->filters->segments as $segment) {
            $this->categories->push(
                $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to)
            );

            $this->query($segment)
                ->calculateSurvivalRates()
                ->each(function ($dataset) {
                    $this->series
                        ->pushOnToSeriesData('Including first 24 hours', $dataset->get('including24Hours'))
                        ->pushOnToSeriesData('After first 24 hours', $dataset->get('after24Hours'));
                });

            if ($this->filters->compare) {
                $this->categories->push(
                    $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to)
                );

                $this->compareQuery($segment)
                    ->calculateSurvivalRates()
                    ->each(function ($dataset) {
                        $this->series
                            ->pushOnToSeriesData('Including first 24 hours', $dataset->get('including24Hours'))
                            ->pushOnToSeriesData('After first 24 hours', $dataset->get('after24Hours'));
                    });
            }
        }
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as `total`')
            ->selectRaw("sum(if(`disposition` = 'Pending', 1, 0)) as `pending`")
            ->selectRaw("sum(if(`disposition` = 'Released', 1, 0)) as `released`")
            ->selectRaw("sum(if(`disposition` = 'Transferred', 1, 0)) as `transferred`")
            ->selectRaw("sum(if(`disposition` = 'Dead on arrival', 1, 0)) as `doa`")
            ->selectRaw("sum(if(`disposition` = 'Died in 24hr', 1, 0)) as `died_in_24`")
            ->selectRaw("sum(if(`disposition` = 'Euthanized in 24hr', 1, 0)) as `euthanized_in_24`")
            ->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return new SurvivalRateCollection($query->get()->mapInto(DataSet::class));
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as `total`')
            ->selectRaw("sum(if(`disposition` = 'Pending', 1, 0)) as `pending`")
            ->selectRaw("sum(if(`disposition` = 'Released', 1, 0)) as `released`")
            ->selectRaw("sum(if(`disposition` = 'Transferred', 1, 0)) as `transferred`")
            ->selectRaw("sum(if(`disposition` = 'Dead on arrival', 1, 0)) as `doa`")
            ->selectRaw("sum(if(`disposition` = 'Died in 24hr', 1, 0)) as `died_in_24`")
            ->selectRaw("sum(if(`disposition` = 'Euthanized in 24hr', 1, 0)) as `euthanized_in_24`")
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to);

        $this->withSegment($query, $segment);

        return new SurvivalRateCollection($query->get()->mapInto(DataSet::class));
    }
}
