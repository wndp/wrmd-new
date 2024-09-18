<?php

namespace App\Analytics\Charts;

use App\Analytics\AreaRangeCollection;
use App\Analytics\Categories;
use App\Analytics\Concerns\HandleChartPeriod;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Models\Incident;
use Illuminate\Support\Collection;

class DurationRangeAndAverage extends Chart
{
    use HandleSeriesNames;
    use HandleChartPeriod;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $period = $this->chartPeriod($this->filters);
        $this->categories = (new Categories())->forTimePeriod($period, $this->filters);

        $data = $this->query()
            ->aggregateData()
            ->padDates($period)
            ->groupByTimePeriod($this->filters->group_by_period);

        $this->series->addSeries(
            $this->formatSeriesName(null, 'Average', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
            $data->sumSeriesGroup([new AreaRangeCollection(), 'averageFromAggregate']),
            [
                'zIndex' => 1,
            ]
        );

        $this->series->addSeries(
            $this->formatSeriesName(null, 'Range', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
            $data->sumSeriesGroup([new AreaRangeCollection(), 'rangeFromAggregate']),
            [
                'type' => 'arearange',
                'linkedTo' => ':previous',
                'zIndex' => 0,
                'fillOpacity' => 0.3,
                'lineWidth' => 0,
                'marker' => [
                    'enabled' => false,
                ],
            ]
        );
    }

    /**
     * Query for the requested data.
     */
    public function query(): Collection
    {
        $query = Incident::where('team_id', $this->team->id)
            ->selectRaw('duration_of_call as aggregate, date(occurred_at) as date')
            ->where('duration_of_call', '>', 0)
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'occurred_at');
        }

        return new AreaRangeCollection($query->get()->mapInto(DataSet::class));
    }
}
