<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use Illuminate\Support\Collection;

class PatientAdmissionAges extends Chart
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
    public function query($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->addSelect('age_unit as subgroup')
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('age_unit')
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
    public function compareQuery($segment): Collection
    {
        if (! $this->filters->compare) {
            return new Collection();
        }

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->addSelect('age_unit as subgroup')
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('age_unit')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->groupBy('date')
            ->groupBy('subgroup')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
