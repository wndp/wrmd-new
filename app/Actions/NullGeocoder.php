<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Geocoder;
use App\ValueObjects\GeocodeComponents;
use CommerceGuys\Addressing\Address;

class NullGeocoder implements Geocoder
{
    use AsAction;

    public function handle(Address $address)
    {
        return new GeocodeComponents(0, 0);
    }
}
