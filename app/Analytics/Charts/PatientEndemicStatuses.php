<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Models\Admission;
use Illuminate\Database\Eloquent\Collection;

class PatientEndemicStatuses extends Chart
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
            ->selectRaw('date_admitted_at as date')
            ->addSelect('native_distributions')
            ->joinPatients()
            ->joinTaxa()
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($this->aggregateData($query->get())->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date_admitted_at as date')
            ->addSelect('native_distributions')
            ->joinPatients()
            ->joinTaxa()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($this->aggregateData($query->get())->mapInto(DataSet::class));
    }

    public function aggregateData($data)
    {
        return $data->map(function ($admission) {
            $admission->subgroup = in_array('US-CA', json_decode($admission->native_distributions) ?? []) ? 'Native' : 'Non-native';

            return $admission;
        })
            ->filter(function ($admission) {
                return ! is_null($admission->subgroup);
            })
            ->groupBy(['date', 'subgroup'])
            ->map(function ($group, $date) {
                return $group->map(function ($admissions, $group) use ($date) {
                    return (object) [
                        'date' => $date,
                        'aggregate' => $admissions->count(),
                        'subgroup' => $group,
                    ];
                });
            })
            ->flatten();
    }
}
