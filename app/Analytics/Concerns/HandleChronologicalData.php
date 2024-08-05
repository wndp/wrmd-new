<?php

namespace App\Analytics\Concerns;

use App\Analytics\Categories;

trait HandleChronologicalData
{
    use HandleSeriesNames;
    use HandleChartPeriod;

    /**
     * Default method to create a response for chronological series data.
     *
     * @param  \Carbon\Carbon|null  $start
     */
    public function handleChronologicalData(string $seriesName = null, $startDate = null): void
    {
        $period = $this->chartPeriod($this->filters, $startDate);

        $this->categories = (new Categories())->forTimePeriod($period, $this->filters);

        foreach ($this->filters->segments as $segment) {
            $this->series->addSeries(
                $this->formatSeriesName($segment, $seriesName ?? $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                $this->query($segment, $period)
                    ->padDates($period)
                    ->groupByTimePeriod($this->filters->group_by_period)
                    ->sumSeriesGroup()
            );

            if ($this->filters->compare) {
                $this->series->addSeries(
                    $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    $this->compareQuery($segment, $period)
                        ->padComparativeDates($this->filters->compare_date_from, $period->count())
                        ->groupByTimePeriod($this->filters->group_by_period)
                        ->sumSeriesGroup()
                );
            }
        }
    }

    /**
     * Default method to create a response for chronological series data with sub groups / categories.
     */
    public function handleChronologicalDataWithSubGroups(): void
    {
        $period = $this->chartPeriod($this->filters);

        $this->categories = (new Categories())->forTimePeriod($period, $this->filters);

        foreach ($this->filters->segments as $segment) {
            $this->query($segment)
                ->padDates($period)
                ->groupByTimePeriodAndGroups($this->filters->group_by_period)
                ->sumSeriesGroups()
                ->each(function ($collection, $subgroup) use ($segment) {
                    $this->series->addSeries(
                        $this->formatSeriesName($segment, $subgroup, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                        $collection
                    );
                });

            if ($this->filters->compare) {
                $this->compareQuery($segment)
                    ->padComparativeDates($this->filters->compare_date_from, $period->count())
                    ->groupByTimePeriodAndGroups($this->filters->group_by_period)
                    ->sumSeriesGroups()
                    ->each(function ($collection, $subgroup) use ($segment) {
                        $this->series->addSeries(
                            $this->formatSeriesName($segment, $subgroup, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                            $collection
                        );
                    });
            }
        }
    }
}
