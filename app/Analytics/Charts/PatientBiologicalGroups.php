<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Models\Admission;
use Illuminate\Support\Collection;

class PatientBiologicalGroups extends Chart
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
    public function query($segment): \Illuminate\Database\Eloquent\Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('date_admitted_at as date')
            ->addSelect('lay_groups')
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
    public function compareQuery($segment): \Illuminate\Database\Eloquent\Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('date_admitted_at as date')
            ->addSelect('lay_groups')
            ->joinPatients()
            ->joinTaxa()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($this->aggregateData($query->get())->mapInto(DataSet::class));
    }

    public function aggregateData($data)
    {
        $groups = trans('terminology.lay_groups.Group');

        return $data->map(function ($admission) use ($groups) {
            $admission->subgroup = (new Collection(json_decode($admission->lay_groups)))->intersect($groups)->first();

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
