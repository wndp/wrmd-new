<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class PatientsBySex extends Chart
{
    use HandlePieData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handlePieData();
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, sex as subgroup')
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('sex')
            ->orderByDesc('aggregate')
            ->groupBy('sex');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, sex as subgroup')
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('sex')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->orderByDesc('aggregate')
            ->groupBy('sex');

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
