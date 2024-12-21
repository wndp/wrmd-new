<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Exceptions\GeocodingException;
use App\Geocoder;
use App\Repositories\AdministrativeDivision;
use CommerceGuys\Addressing\Address;
use Geocodio\Exceptions\GeocodioException;
use Geocodio\GeocodioFacade;

class GeocodioGeocoder implements Geocoder
{
    use AsAction;

    public function handle(Address $address)
    {
        throw_unless(
            in_array($address->getCountryCode(), ['US', 'CA']),
            GeocodingException::class,
            "geocode.io can not geocode addresses from [{$address->getCountryCode()}]"
        );

        $address->withLocale($address->getCountryCode());

        try {
            $formatter = AdministrativeDivision::addressFormater();

            $results = collect(
                GeocodioFacade::geocode($formatter->format($address))->results
            );
        } catch (GeocodioException $e) {
            throw new GeocodingException('Bad response from geocod.io: ['.$e->getMessage().']');
        }

        $response = $results->firstWhere('accuracy', '>=', 0.8);

        if (is_null($response)) {
            $response = $results->sortByDesc('accuracy')->first();
        }

        if (is_null($response)) {
            return NullGeocoder::run($address);
        }

        return new GeocodeComponents(
            $response->location->lat,
            $response->location->lng,
            object_get($response, 'address_components.county')
        );
    }
}
