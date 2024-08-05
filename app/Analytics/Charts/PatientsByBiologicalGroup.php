<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use Illuminate\Support\Collection;

class PatientsByBiologicalGroup extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $segment = $this->filters->segments[0];
        $groups = trans('terminology.lay_groups.Group');

        $data = $this->query($segment)->groupBy(function ($admission) use ($groups) {
            return (new Collection(json_decode($admission->lay_groups)))->intersect($groups)->first();
        });

        $data->offsetUnset('');

        $data = $data->map(function ($group, $name) use ($segment) {
            return (object) [
                'group' => $this->formatSeriesName($segment, $name, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                'aggregate' => $group->count(),
            ];
        })
            ->sortByDesc('aggregate')
            ->take(5)
            ->pluck('aggregate', 'group');

        if ($this->filters->compare) {
            $compareData = $this->compareQuery($segment)->groupBy(function ($admission) use ($groups) {
                return (new Collection(json_decode($admission->lay_groups)))->intersect($groups)->first();
            });

            $compareData->offsetUnset('');

            $compareData = $compareData->map(function ($group, $name) use ($segment) {
                return (object) [
                    'group' => $this->formatSeriesName($segment, $name, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    'aggregate' => $group->count(),
                ];
            })
                ->sortByDesc('aggregate')
                ->take(5)
                ->pluck('aggregate', 'group');
        }

        $this->series->addPieSeriesData($data)
            ->addPieSeriesData($compareData ?? new Collection());
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('lay_groups', 'taxon_id')
            ->joinPatients()
            ->join('species', 'patients.taxon_id', '=', 'species.id')
            ->where('class', 'Aves');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('lay_groups', 'taxon_id')
            ->joinPatients()
            ->join('species', 'patients.taxon_id', '=', 'species.id')
            ->where('class', 'Aves')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to);

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
