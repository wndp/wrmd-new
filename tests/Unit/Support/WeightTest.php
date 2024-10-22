<?php

namespace Tests\Unit\Support;

use App\Support\Weight;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

final class WeightTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    #[Test]
    public function itConvertsWeightsIntoKilograms(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(10.00, Weight::toKilograms(10, $kgWeightId));
        $this->assertSame(0.01, Weight::toKilograms(10, $gWeightId));
        $this->assertSame(4.5351, Weight::toKilograms(10, $lbWeightId));
        $this->assertSame(0.2835, Weight::toKilograms(10, $ozWeightId));
    }

    #[Test]
    public function itConvertsWeightsIntoGrams(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(10000.00, Weight::toGrams(10, $kgWeightId));
        $this->assertSame(10.00, Weight::toGrams(10, $gWeightId));
        $this->assertSame(4535.92, Weight::toGrams(10, $lbWeightId));
        $this->assertSame(283.50, Weight::toGrams(10, $ozWeightId));
    }

    #[Test]
    public function itConvertsWeightsIntoPounds(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(22.05, Weight::toPounds(10, $kgWeightId));
        $this->assertSame(0.022, Weight::toPounds(10, $gWeightId));
        $this->assertSame(10.00, Weight::toPounds(10, $lbWeightId));
        $this->assertSame(0.625, Weight::toPounds(10, $ozWeightId));
    }

    #[Test]
    public function itConvertsWeightsIntoOunces(): void
    {
        [$kgWeightId, $gWeightId, $lbWeightId, $ozWeightId] = $this->weightUnits();

        $this->assertSame(352.74, Weight::toOunces(10, $kgWeightId));
        $this->assertSame(0.3527, Weight::toOunces(10, $gWeightId));
        $this->assertSame(160.00, Weight::toOunces(10, $lbWeightId));
        $this->assertSame(10.00, Weight::toOunces(10, $ozWeightId));
    }
}
