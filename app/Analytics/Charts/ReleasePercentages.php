<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class ReleasePercentages extends Chart
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
            ->selectRaw('count(*) as aggregate, release_type as subgroup')
            ->joinPatients()
            ->whereNotNull('release_type')
            ->where('disposition', '=', 'Released')
            ->orderByDesc('aggregate')
            ->groupBy('release_type')
            ->limit(10);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, release_type as subgroup')
            ->joinPatients()
            ->whereNotNull('release_type')
            ->where('disposition', '=', 'Released')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->orderByDesc('aggregate')
            ->groupBy('release_type')
            ->limit(10);

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
