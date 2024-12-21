<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Exceptions\GeocodingException;
use App\Geocoder;
use App\ValueObjects\GeocodeComponents;
use CommerceGuys\Addressing\Address;
use Geocodio\Exceptions\GeocodioException;
use Geocodio\GeocodioFacade;

class NullGeocoder implements Geocoder
{
    use AsAction;

    public function handle(Address $address)
    {
        return new GeocodeComponents(0, 0);
    }
}
