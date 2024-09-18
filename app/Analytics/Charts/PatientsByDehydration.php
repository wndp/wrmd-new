<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class PatientsByDehydration extends Chart
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
            ->selectRaw('count(*) as aggregate, dehydration as subgroup')
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('dehydration')
            ->orderByDesc('aggregate')
            ->groupBy('dehydration');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, dehydration as subgroup')
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('dehydration')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderByDesc('aggregate')
            ->groupBy('dehydration');

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
