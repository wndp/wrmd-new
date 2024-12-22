<?php

namespace Tests\Feature\Geocoding;

use App\Actions\WrmdGeocoder;
use App\Models\Patient;
use App\ValueObjects\GeocodeComponents;
use App\ValueObjects\SingleStorePoint;
use CommerceGuys\Addressing\Address;
use Tests\TestCase;

final class WrmdGeocoderTest extends TestCase
{
    public function test_it_returns_an_predictable_geocode_components_object_for_a_patients_location_found(): void
    {
        Patient::factory()->create([
            'address_found' => '21428 Jerusalem Grade',
            'city_found' => 'Lower Lake',
            'subdivision_found' => 'CA',
            'postal_code_found' => '95457',
            'county_found' => 'Lake County',
            'coordinates_found' => new SingleStorePoint(38.82296602, -122.51286703),
        ]);

        $address = (new Address)
            ->withCountryCode('US')
            ->withAdministrativeArea('CA')
            ->withLocality('Lower Lake')
            ->withAddressLine1('21428 Jerusalem Grade')
            ->withPostalCode('95457');

        $response = WrmdGeocoder::run($address);

        $this->assertEquals(new GeocodeComponents(38.82296602, -122.51286703, 'Lake County'), $response);
    }

    public function test_it_returns_an_predictable_geocode_components_object_for_a_patients_disposition_location(): void
    {
        Patient::factory()->create([
            'disposition_address' => '21428 Jerusalem Grade',
            'disposition_city' => 'Lower Lake',
            'disposition_subdivision' => 'CA',
            'disposition_postal_code' => '95457',
            'disposition_county' => 'Lake County',
            'disposition_coordinates' => new SingleStorePoint(38.82296602, -122.51286703),
        ]);

        $address = (new Address)
            ->withCountryCode('US')
            ->withAdministrativeArea('CA')
            ->withLocality('Lower Lake')
            ->withAddressLine1('21428 Jerusalem Grade')
            ->withPostalCode('95457');

        $response = WrmdGeocoder::run($address);

        $this->assertEquals(new GeocodeComponents(38.82296602, -122.51286703, 'Lake County'), $response);
    }
}
