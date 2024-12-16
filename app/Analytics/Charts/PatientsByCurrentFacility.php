<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Models\Admission;

class PatientsByCurrentFacility extends Chart
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
            ->selectRaw('count(*) as aggregate, facility as subgroup')
            ->joinPatients()
            ->leftJoinCurrentLocation()
            ->whereNotNull('facility')
            ->orderByDesc('aggregate')
            ->groupBy('facility');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, facility as subgroup')
            ->joinPatients()
            ->leftJoinCurrentLocation()
            ->whereNotNull('facility')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderByDesc('aggregate')
            ->groupBy('facility');

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
