<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;

class PatientsByEndemicStatus extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series->addPieSeriesData(
                $this->query($segment)
                    ->mapToGroups(function ($admission) {
                        $key = in_array('US-CA', json_decode($admission->native_distributions) ?? []) ? 'Native' : 'Non-native';

                        return [$key => $admission];
                    })
                    ->map(function ($group, $status) use ($segment) {
                        return (object) [
                            'group' => $this->formatSeriesName($segment, $status, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                            'aggregate' => $group->count(),
                        ];
                    })
                    ->pluck('aggregate', 'group')
            );

            if ($this->filters->compare) {
                $this->series->addPieSeriesData(
                    $this->compareQuery($segment)
                        ->mapToGroups(function ($admission) {
                            $key = in_array('US-CA', json_decode($admission->native_distributions) ?? []) ? 'Native' : 'Non-native';

                            return [$key => $admission];
                        })->map(function ($group, $status) use ($segment) {
                            return (object) [
                                'group' => $this->formatSeriesName($segment, $status, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                                'aggregate' => $group->count(),
                            ];
                        })
                        ->pluck('aggregate', 'group')
                );
            }
        }
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('native_distributions', 'taxon_id')
            ->joinPatients()
            ->join('species', 'patients.taxon_id', '=', 'species.id');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('native_distributions', 'taxon_id')
            ->joinPatients()
            ->join('species', 'patients.taxon_id', '=', 'species.id')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
