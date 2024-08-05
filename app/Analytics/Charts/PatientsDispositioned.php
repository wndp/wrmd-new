<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;

class PatientsDispositioned extends Chart
{
    use HandleChronologicalData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handleChronologicalDataWithSubGroups();
    }

    /**
     * Query for the requested data.
     */
    public function query($segment): ChronologicalCollection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date(dispositioned_at) as date, disposition as subgroup')
            ->joinPatients()
            ->where('disposition', '!=', 'Pending')
            ->groupBy('date')
            ->groupBy('subgroup')
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'dispositioned_at');
        }

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): ChronologicalCollection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date(dispositioned_at) as date, disposition as subgroup')
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'dispositioned_at')
            ->where('disposition', '!=', 'Pending')
            ->groupBy('date')
            ->groupBy('subgroup')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
