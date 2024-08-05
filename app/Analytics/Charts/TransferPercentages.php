<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class TransferPercentages extends Chart
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
            ->selectRaw('count(*) as aggregate, transfer_type as subgroup')
            ->joinPatients()
            ->whereNotNull('transfer_type')
            ->where('disposition', '=', 'Transferred')
            ->groupBy('transfer_type')
            ->orderByDesc('aggregate')
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
            ->selectRaw('count(*) as aggregate, transfer_type as subgroup')
            ->joinPatients()
            ->whereNotNull('transfer_type')
            ->where('disposition', '=', 'Transferred')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->groupBy('transfer_type')
            ->orderByDesc('aggregate')
            ->limit(10);

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
