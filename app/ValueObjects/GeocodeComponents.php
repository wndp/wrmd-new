<?php

namespace App\ValueObjects;

class GeocodeComponents
{
    public function __construct(
        public $latitude,
        public $longitude,
        public $administrativeAreaLevel2 = '',
        public $administrativeAreaLevel3 = '',
        public $administrativeAreaLevel4 = '',
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->administrativeAreaLevel2 = $administrativeAreaLevel2;
        $this->administrativeAreaLevel3 = $administrativeAreaLevel3;
        $this->administrativeAreaLevel4 = $administrativeAreaLevel4;
    }
}
