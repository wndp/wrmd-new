<?php

namespace App\Analytics\Maps;

use App\Analytics\Contracts\Map;
use App\Domain\Hotline\Models\Incident as IncidentModel;

class Incident extends Map
{
    public function compute()
    {
        $incident = IncidentModel::findOrFail($this->filters->id);

        if (is_null($incident->coordinates)) {
            $this->markers = [];
        } else {
            $this->series->push(
                [
                    'name' => $incident->incident_number,
                    'data' => [[
                        'coordinates' => [
                            'lat' => $incident->coordinates->latitude,
                            'lng' => $incident->coordinates->longitude,
                        ],
                        'title' => "{$incident->incident_number} {$incident->suspected_species}",
                    ]],
                ]
            );
        }
    }
}
