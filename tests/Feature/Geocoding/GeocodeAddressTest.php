<?php

namespace Tests\Feature\Geocoding;

use App\Casts\SingleStorePoint;
use App\Jobs\GeocodeAddress;
use App\Models\Patient;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GeocodeAddressTest extends TestCase
{
    #[Test]
    public function it_geocodes_an_address_using_one_of_the_geocoders(): void
    {
        $patient = Patient::factory()->create([
            'subdivision_found' => 'CA',
            'city_found' => 'Lower Lake',
            'address_found' => '21428 Jerusalem Grade',
        ]);

        $this->assertNull($patient->coordinates_found);

        GeocodeAddress::dispatchSync($patient, 'coordinates_found');

        $this->assertInstanceOf(SingleStorePoint::class, $patient->refresh()->coordinates_found);
        $this->assertEquals(0.00000004, $patient->coordinates_found->latitude);
        $this->assertEquals(0.00000004, $patient->coordinates_found->longitude);
        //$this->assertEquals(null, $patient->admin);
    }
}
