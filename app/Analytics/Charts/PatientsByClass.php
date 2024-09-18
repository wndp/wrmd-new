<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;

class PatientsByClass extends Chart
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
            ->joinPatients()
            ->joinTaxa()
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->addSelect('class as subgroup')
            ->groupBy('date')
            ->orderBy('date')
            ->groupBy('subgroup');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
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
            ->selectRaw('count(*) as aggregate, date( admitted_at) as date')
            ->addSelect('class as subgroup')
            ->joinPatients()
            ->joinTaxa()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->groupBy('date')
            ->orderBy('date')
            ->groupBy('subgroup');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
