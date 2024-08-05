<?php

namespace App\Analytics;

use JsonSerializable;

class MapIntoMapMarkers implements JsonSerializable
{
    protected $admission;

    public function __construct($admission)
    {
        $this->admission = $admission;
    }

    public function jsonSerialize()
    {
        return [
            'coordinates' => [
                'lat' => $this->admission->patient->coordinates_found->latitude,
                'lng' => $this->admission->patient->coordinates_found->longitude,
            ],
            'title' => "{$this->admission->case_number} {$this->admission->patient->common_name}",
            'content' => $this->admission->patient->location_found,
            'link' => $this->admission->href,
        ];
    }
}
