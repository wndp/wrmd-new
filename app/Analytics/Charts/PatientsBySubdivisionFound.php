<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Models\Admission;

class PatientsBySubdivisionFound extends Chart
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
            ->selectRaw('count(*) as aggregate, subdivision_found as subgroup')
            ->joinPatients()
            ->whereNotNull('subdivision_found')
            ->orderByDesc('aggregate')
            ->groupBy('subdivision_found')
            ->limit(10);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, subdivision_found as subgroup')
            ->joinPatients()
            ->whereNotNull('subdivision_found')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderByDesc('aggregate')
            ->groupBy('subdivision_found')
            ->limit(10);

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
