<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class PatientsByCurrentArea extends Chart
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
            ->selectRaw('count(*) as aggregate, area as subgroup')
            ->joinPatients()
            ->joinLastLocation()
            ->where('facility', 'Clinic')
            ->whereNotNull('area')
            ->orderByDesc('aggregate')
            ->groupBy('area');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, area as subgroup')
            ->joinPatients()
            ->joinLastLocation()
            ->where('facility', 'Clinic')
            ->whereNotNull('area')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderByDesc('aggregate')
            ->groupBy('area');

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
