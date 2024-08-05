<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Categories;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChartPeriod;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use Illuminate\Support\Facades\DB;

class PatientsInCare extends Chart
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

        foreach ($this->filters->segments as $segment) {
            $this->series->addSeries(
                $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                $this->query($segment)
                    ->padDates($period)
                    ->groupByTimePeriod($this->filters->group_by_period)
                    ->sumSeriesGroup()
                    ->values()
            );

            if ($this->filters->compare) {
                $this->series->addSeries(
                    $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    $this->compareQuery($segment)
                        ->padComparativeDates($this->filters->compare_date_from, $period->count())
                        ->groupByTimePeriod($this->filters->group_by_period)
                        ->sumSeriesGroup()
                        ->values()
                );
            }
        }
    }

    /**
     * Query for the requested data.
     */
    public function query($segment): ChronologicalCollection
    {
        $from = $this->filters->date_from;
        $to = $this->filters->date_to;

        DB::statement('set @i = -1;');

        $query = Admission::useWritePdo()->owner($this->team->id)
            ->select(DB::raw("count(*) as aggregate, cal.selected_date as date, 'Patients in Care' as segment"))
            ->joinPatients()
            ->crossJoin(
                DB::raw(
                    "(SELECT selected_date FROM (
                            SELECT DATE(ADDDATE('{$from}', INTERVAL @i:=@i+1 DAY)) AS selected_date FROM `patients`
                            HAVING @i < DATEDIFF('{$to}', '{$from}')
                        ) v
                        WHERE selected_date BETWEEN '{$from}' AND '{$to}'
                    ) cal"
                )
            )
            ->whereRaw('date_admitted_at <= cal.selected_date')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('dispositioned_at')->where('disposition', 'Pending');
                })
                    ->orWhere(function ($query) {
                        $query->whereRaw('dispositioned_at >= cal.selected_date')->where('disposition', '!=', 'Pending');
                    });
            })
            ->groupBy('cal.selected_date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): ChronologicalCollection
    {
        $from = $this->filters->compare_date_from;
        $to = $this->filters->compare_date_to;

        DB::statement('set @i = -1;');

        $query = Admission::useWritePdo()->owner($this->team->id)
            ->select(DB::raw("count(*) as aggregate, cal.selected_date as date, 'Patients in Care' as segment"))
            ->joinPatients()
            ->crossJoin(
                DB::raw(
                    "(SELECT selected_date FROM (
                            SELECT DATE(ADDDATE('{$from}', INTERVAL @i:=@i+1 DAY)) AS selected_date FROM `patients`
                            HAVING @i < DATEDIFF('{$to}', '{$from}')
                        ) v
                        WHERE selected_date BETWEEN '{$from}' AND '{$to}'
                    ) cal"
                )
            )
            ->whereRaw('date_admitted_at <= cal.selected_date')
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('dispositioned_at')->where('disposition', 'Pending');
                })
                    ->orWhere(function ($query) {
                        $query->whereRaw('dispositioned_at >= cal.selected_date')->where('disposition', '!=', 'Pending');
                    });
            })
            ->groupBy('cal.selected_date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
