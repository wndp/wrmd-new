<?php

namespace App\Analytics\Segments;

use App\Analytics\Contracts\Segment;

class CityFound extends Segment
{
    public function handle()
    {
        $city = $this->parameters[0];

        $this->query->where('city_found', $city);
    }
}
