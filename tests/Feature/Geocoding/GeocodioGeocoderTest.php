<?php

namespace Tests\Feature\Geocoding;

use App\Actions\GeocodioGeocoder;
use App\ValueObjects\GeocodeComponents;
use CommerceGuys\Addressing\Address;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('integration')]
final class GeocodioGeocoderTest extends TestCase
{
    #[Test]
    public function itReturnsAnPredictableGeocodeComponentsObject(): void
    {
        $address = (new Address())
            ->withCountryCode('US')
            ->withAdministrativeArea('CA')
            ->withLocality('Lower Lake')
            ->withAddressLine1('21428 Jerusalem Grade')
            ->withPostalCode('95457');

        $response = GeocodioGeocoder::run($address);

        $this->assertEquals(new GeocodeComponents(38.82296, -122.512847, 'Lake County'), $response);
    }
}
