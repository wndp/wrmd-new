<?php

namespace Tests\Unit\Support;

use App\Support\Weight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

final class WeightTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_it_converts_weights_into_kilograms(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(10.00, Weight::toKilograms(10, $kgWeightId));
        $this->assertSame(0.01, Weight::toKilograms(10, $gWeightId));
        $this->assertSame(4.5351, Weight::toKilograms(10, $lbWeightId));
        $this->assertSame(0.2835, Weight::toKilograms(10, $ozWeightId));
    }

    public function test_it_converts_weights_into_grams(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(10000.00, Weight::toGrams(10, $kgWeightId));
        $this->assertSame(10.00, Weight::toGrams(10, $gWeightId));
        $this->assertSame(4535.92, Weight::toGrams(10, $lbWeightId));
        $this->assertSame(283.50, Weight::toGrams(10, $ozWeightId));
    }

    public function test_it_converts_weights_into_pounds(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(22.05, Weight::toPounds(10, $kgWeightId));
        $this->assertSame(0.022, Weight::toPounds(10, $gWeightId));
        $this->assertSame(10.00, Weight::toPounds(10, $lbWeightId));
        $this->assertSame(0.625, Weight::toPounds(10, $ozWeightId));
    }

    public function test_it_converts_weights_into_ounces(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(352.74, Weight::toOunces(10, $kgWeightId));
        $this->assertSame(0.3527, Weight::toOunces(10, $gWeightId));
        $this->assertSame(160.00, Weight::toOunces(10, $lbWeightId));
        $this->assertSame(10.00, Weight::toOunces(10, $ozWeightId));
    }
}
