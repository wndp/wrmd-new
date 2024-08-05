<?php

namespace App\Analytics\Maps;

use App\Models\Admission;
use App\Analytics\Contracts\Map;

class DispositionLocation extends Map
{
    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series->push(
                [
                    'name' => $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                    'data' => $this->query($segment)->map(function ($admission) {
                        return [
                            'coordinates' => [
                                'lat' => $admission->patient->disposition_coordinates->latitude,
                                'lng' => $admission->patient->disposition_coordinates->longitude,
                            ],
                            'title' => "{$admission->case_number} {$admission->patient->common_name}",
                            'content' => $admission->patient->disposition_address,
                            'link' => $admission->href,
                        ];
                    }),
                ]
            );

            if ($this->filters->compare) {
                $this->series->push(
                    [
                        'name' => $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                        'data' => $this->compareQuery($segment)->map(function ($admission) {
                            return [
                                'coordinates' => [
                                    'lat' => $admission->patient->disposition_coordinates->latitude,
                                    'lng' => $admission->patient->disposition_coordinates->longitude,
                                ],
                                'title' => "{$admission->case_number} {$admission->patient->common_name}",
                                'content' => $admission->patient->disposition_address,
                                'link' => $admission->href,
                            ];
                        }),
                    ]
                );
            }
        }
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereRaw('disposition_coordinates != POINT(0, 0)')
            ->whereNotNull('disposition_coordinates')
            ->whereNotNull('disposition_address')
            ->whereIn('disposition', ['released', 'transferred']);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $query->with('patient.species')->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereRaw('disposition_coordinates != POINT(0, 0)')
            ->whereNotNull('disposition_coordinates')
            ->whereNotNull('disposition_address')
            ->whereIn('disposition', ['released', 'transferred'])
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to);

        $this->withSegment($query, $segment);

        return $query->with('patient.species')->get();
    }
}
