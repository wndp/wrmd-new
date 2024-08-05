<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Domain\Hotline\Models\Incident;
use Illuminate\Support\Collection;

class TotalCallDuration extends Chart
{
    use HandleChronologicalData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handleChronologicalData('Total Minutes');
    }

    /**
     * Query for the requested data.
     */
    public function query(): Collection
    {
        $query = Incident::where('team_id', $this->team->id)
            ->selectRaw('sum(duration_of_call) as aggregate, date(occurred_at) as date')
            ->groupBy('date')
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'occurred_at');
        }

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery(): Collection
    {
        $query = Incident::where('team_id', $this->team->id)
            ->selectRaw('sum(duration_of_call) as aggregate, date(occurred_at) as date')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'occurred_at')
            ->groupBy('date')
            ->orderBy('date');

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
