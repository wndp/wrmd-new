<?php

namespace App\Analytics\Charts;

use App\Analytics\Categories;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Models\Patient;
use App\Weight;

class DailyWeights extends Chart
{
    use HandleSeriesNames;

    private $patient;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->patient = Patient::findOrFail($this->filters->patientId)->validateOwnership($this->team->id);

        $data = $this->query();

        $this->categories = (new Categories($data))->forDates($this->filters);

        $this->series->addSeries(
            $this->formatSeriesName(
                $this->patient->common_name,
                $this->patient->common_name,
                $this->filters->compare,
                $this->filters->date_from,
                $this->filters->date_to
            ),

            // In the event that multiple weights were record on a single date,
            // get and return the last/most recent one.
            $data->groupByTimePeriod($this->filters->group_by_period)->sumSeriesGroup(function ($data) {
                return $data->last()->aggregate;
            })
        );
    }

    /**
     * Query for the requested data.
     */
    public function query(): ChronologicalCollection
    {
        $this->patient = Patient::findOrFail($this->filters->patientId)->validateOwnership($this->team->id);

        $data = $this->patient
            ->getWeights()
            ->map(function ($weight) {
                return new DataSet([
                    'aggregate' => Weight::toGrams($weight->weight, $weight->unit),
                    'date' => $weight->weighed_at_date,
                    'subgroup' => 'Daily Weights',
                ]);
            });

        return new ChronologicalCollection($data);
    }
}
