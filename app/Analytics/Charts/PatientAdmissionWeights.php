<?php

namespace App\Analytics\Charts;

use App\Analytics\AreaRangeCollection;
use App\Analytics\Categories;
use App\Analytics\Concerns\HandleChartPeriod;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Models\Admission;
use App\Weight;
use Illuminate\Support\Collection;

class PatientAdmissionWeights extends Chart
{
    use HandleChartPeriod;
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $period = $this->chartPeriod($this->filters);
        $this->categories = (new Categories)->forTimePeriod($period, $this->filters);

        $colors = ['#74ABD4', '#AFCD6D', '#E6BB5F', '#F196A5', '#3B87C0', '#8DB13D', '#D79D22', '#EF8999', '#FCD02B'];

        $i = 0;
        foreach ($this->filters->segments as $segment) {
            $data = $this->query($segment)
                ->map(function ($data) {
                    return [
                        'date' => $data->date,
                        'aggregate' => Weight::toGrams($data->aggregate, $data->weight_unit),
                    ];
                })
                ->aggregateData()
                ->padDates($period)
                ->groupByTimePeriod($this->filters->group_by_period);

            $this->series->addSeries(
                $this->formatSeriesName($segment, 'Average', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                $data->sumSeriesGroup([new AreaRangeCollection, 'averageFromAggregate']),
                [
                    'zIndex' => 1,
                    'color' => $colors[$i],
                ]
            );

            $this->series->addSeries(
                $this->formatSeriesName($segment, 'Range', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                $data->sumSeriesGroup([new AreaRangeCollection, 'rangeFromAggregate']),
                [
                    'type' => 'arearange',
                    'linkedTo' => ':previous',
                    'zIndex' => 0,
                    'color' => $colors[$i],
                    'fillOpacity' => 0.3,
                    'lineWidth' => 0,
                    'marker' => [
                        'enabled' => false,
                    ],
                ]
            );

            if ($this->filters->compare) {
                $i++;

                $data = $this->compareQuery($segment)
                    ->map(function ($data) {
                        return [
                            'date' => $data->date,
                            'aggregate' => Weight::toGrams($data->aggregate, $data->weight_unit),
                        ];
                    })
                    ->aggregateData()
                    ->padDates($period)
                    ->groupByTimePeriod($this->filters->group_by_period);

                $this->series->addSeries(
                    $this->formatSeriesName($segment, 'Average', $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    $data->sumSeriesGroup([new AreaRangeCollection, 'averageFromAggregate']),
                    [
                        'zIndex' => 1,
                        'color' => $colors[$i],
                    ]
                );

                $this->series->addSeries(
                    $this->formatSeriesName($segment, 'Range', $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    $data->sumSeriesGroup([new AreaRangeCollection, 'rangeFromAggregate']),
                    [
                        'type' => 'arearange',
                        'linkedTo' => ':previous',
                        'zIndex' => 0,
                        'color' => $colors[$i],
                        'fillOpacity' => 0.3,
                        'lineWidth' => 0,
                        'marker' => [
                            'enabled' => false,
                        ],
                    ]
                );
            }
            $i++;
        }
    }

    /**
     * Query for the requested data.
     */
    public function query($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('weight as aggregate, weight_unit, date(examined_at) as date')
            ->joinPatients()
            ->joinIntakeExam()
            ->where('weight', '>', 0)
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'examined_at');
        }

        $this->withSegment($query, $segment);

        return new AreaRangeCollection($query->get()->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('weight as aggregate, weight_unit, date(examined_at) as date')
            ->joinPatients()
            ->joinIntakeExam()
            ->where('weight', '>', 0)
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'examined_at')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new AreaRangeCollection($query->get()->mapInto(DataSet::class));
    }
}
