<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;

class PatientsCurrentFacility extends Chart
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
            ->selectRaw('count(*) as aggregate, date(moved_in_at) as date, facility as subgroup')
            ->joinPatients()
            ->joinLastLocation()
            ->whereNotNull('facility')
            ->groupBy('date')
            ->groupBy('subgroup')
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
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
            ->selectRaw('count(*) as aggregate, date(moved_in_at) as date, facility as subgroup')
            ->joinPatients()
            ->joinLastLocation()
            ->whereNotNull('facility')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->groupBy('date')
            ->groupBy('subgroup')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
