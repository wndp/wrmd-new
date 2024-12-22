<?php

namespace Tests\Feature\Geocoding;

use App\Actions\NullGeocoder;
use App\ValueObjects\GeocodeComponents;
use CommerceGuys\Addressing\Address;
use Tests\TestCase;

final class NullGeocoderTest extends TestCase
{
    public function test_it_returns_an_empty_geocode_components_object(): void
    {
        $response = NullGeocoder::run(new Address);

        $this->assertEquals(new GeocodeComponents(0, 0), $response);
    }
}
