<?php

namespace App\Analytics\Maps;

use App\Analytics\Contracts\Map;
use App\Models\Incident;

class IncidentLocations extends Map
{
    public function compute()
    {
        $this->series->push(
            [
                'name' => $this->formatSeriesName('Incidents', 'Incidents', $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                'data' => $this->query()->map(function ($incident) {
                    return [
                        'coordinates' => [
                            'lat' => $incident->incident_coordinates?->latitude,
                            'lng' => $incident->incident_coordinates?->longitude,
                        ],
                        'title' => "{$incident->incident_number} {$incident->suspected_species}",
                        'content' => $incident->getCoordinatesSpatialData()['address']->format(),
                        //'link' => $admission->href
                    ];
                }),
            ]
        );

        if ($this->filters->compare) {
            $this->series->push(
                [
                    'name' => $this->formatSeriesName('Incidents', 'Incidents', $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                    'data' => $this->compareQuery()->map(function ($incident) {
                        return [
                            'coordinates' => [
                                'lat' => $incident->incident_coordinates?->latitude,
                                'lng' => $incident->incident_coordinates?->longitude,
                            ],
                            'title' => "{$incident->incident_number} {$incident->suspected_species}",
                            'content' => $incident->getCoordinatesSpatialData()['address']->format(),
                            //'link' => $admission->href
                        ];
                    }),
                ]
            );
        }
    }

    public function query()
    {
        $query = Incident::where('team_id', $this->team->id)
            ->whereRaw('incident_coordinates != GEOGRAPHY_POINT(0, 0)')
            ->whereNotNull('incident_coordinates')
            ->whereNotNull('incident_address');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'occurred_at');
        }

        return $query->get();
    }

    public function compareQuery()
    {
        return Incident::where('team_id', $this->team->id)
            ->whereRaw('incident_coordinates != GEOGRAPHY_POINT(0, 0)')
            ->whereNotNull('incident_coordinates')
            ->whereNotNull('incident_address')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'occurred_at')
            ->get();
    }
}
