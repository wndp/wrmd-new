<?php

namespace App\Analytics\Maps;

use App\Analytics\Contracts\Map;
use App\Analytics\Series;
use App\Models\Team;

class Account extends Map
{
    public function compute()
    {
        $team = Team::find($this->team->id);

        if ($team->coordinates) {
            $this->series = (new Series())->push(
                [
                    //'name' => 'test',
                    'data' => [$this->formatMarkers($team)],
                ]
            );
        }
    }

    public function formatMarkers($team)
    {
        return [
            'coordinates' => [
                'lat' => $team->coordinates->latitude,
                'lng' => $team->coordinates->longitude,
            ],
            'title' => $team->organization,
            'content' => $team->full_address,
            'link' => '',
        ];
    }
}
