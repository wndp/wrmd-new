<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Models\Incident;

class IncidentsByStatus extends Chart
{
    use HandlePieData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handlePieData();
    }

    public function query()
    {
        $query = Incident::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, incident_status_id as subgroup')
            ->orderByDesc('aggregate')
            ->groupBy('incident_status_id')
            ->limit(5);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'occurred_at');
        }

        return $query->get();
    }

    public function compareQuery()
    {
        $query = Incident::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, incident_status_id as subgroup')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'occurred_at')
            ->orderByDesc('aggregate')
            ->groupBy('incident_status_id')
            ->limit(5);

        return $query->get();
    }
}
