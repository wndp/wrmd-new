<?php

namespace Tests\Feature\Geocoding;

use App\Actions\GeocodioGeocoder;
use App\ValueObjects\GeocodeComponents;
use CommerceGuys\Addressing\Address;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('integration')]
final class GeocodioGeocoderTest extends TestCase
{
    public function test_it_returns_an_predictable_geocode_components_object(): void
    {
        $address = (new Address)
            ->withCountryCode('US')
            ->withAdministrativeArea('CA')
            ->withLocality('Lower Lake')
            ->withAddressLine1('21428 Jerusalem Grade')
            ->withPostalCode('95457');

        $response = GeocodioGeocoder::run($address);

        $this->assertEquals(new GeocodeComponents(38.82296, -122.512847, 'Lake County'), $response);
    }
}
