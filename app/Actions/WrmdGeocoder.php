<?php

namespace App\Actions;

use App\Concerns\AsAction;
use App\Exceptions\GeocodingException;
use App\Geocoder;
use App\Models\Patient;
use App\ValueObjects\GeocodeComponents;
use CommerceGuys\Addressing\Address;
use Geocodio\Exceptions\GeocodioException;
use Geocodio\GeocodioFacade;

class WrmdGeocoder implements Geocoder
{
    use AsAction;

    public function handle(Address $address)
    {
        try {
            return $this->locationFound($address);
        } catch (\Exception $e) {
        }

        try {
            return $this->dispositionlocation($address);
        } catch (\Exception $e) {
        }

        throw new GeocodingException('No matching address found in WRMD.');
    }

    /**
     * Search the patient location found attributes.
     */
    public function locationFound(Address $address): GeocodeComponents
    {
        $result = Patient::where(array_filter([
            'address_found' => $address->getAddressLine1(),
            'subdivision_found' => $address->getAdministrativeArea(),
            'city_found' => $address->getLocality(),
            'postal_code_found' => $address->getPostalCode(),
        ]))
            ->whereNotNull('coordinates_found')
            ->firstOrFail();

        throw_if(
            $result->coordinates_found->latitude > 90 || $result->coordinates_found->longitude > 180,
            new GeocodingException('Coordinates out of bounds.')
        );

        return new GeocodeComponents(
            $result->coordinates_found->latitude,
            $result->coordinates_found->longitude,
            $result->county_found
        );
    }

    /**
     * Search the patient disposition location attributes.
     */
    public function dispositionlocation(Address $address): GeocodeComponents
    {
        $result = Patient::where(arrray_filter([
            'disposition_address' => $address->getAddressLine1(),
            'disposition_city' => $address->getLocality(),
            'disposition_subdivision' => $address->getAdministrativeArea(),
            'disposition_postal_code' => $address->getPostalCode(),
        ]))
            ->whereNotNull('disposition_coordinates')
            ->firstOrFail();

        throw_if(
            $result->disposition_coordinates->latitude > 90 || $result->disposition_coordinates->longitude > 180,
            new GeocodingException('Coordinates out of bounds.')
        );

        return new GeocodeComponents(
            $result->disposition_coordinates->latitude,
            $result->disposition_coordinates->longitude,
            $result->county_found
        );
    }
}
