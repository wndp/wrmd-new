<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Number;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SurvivalRateTest extends TestCase
{
    #[Test]
    public function itCalculatesTheSurvivalRateFromAnObjectOfDispositionAttributes(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function itCalculatesTheSurvivalRateFromACollectionOfObjectsWithDispositionAttributes(): void
    {
        $result = Number::survivalRate((object) collect([
            [
                'pending' => 10,
                'total' => 10,
            ],
            [
                'pending' => 5,
                'total' => 5,
            ],
        ]));

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function doaIsSubtractedFromTheTotal(): void
    {
        $result = Number::survivalRate((object) [
            'doa' => 2,
            'pending' => 8,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function pendingIsSummedIntoTheNumerator(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function releasedIsSummedIntoTheNumerator(): void
    {
        $result = Number::survivalRate((object) [
            'released' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function transferredIsSummedIntoTheNumerator(): void
    {
        $result = Number::survivalRate((object) [
            'transferred' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function diedIn24IsSummedIntoTheDenominator(): void
    {
        $result = Number::survivalRate((object) [
            'died_in_24' => 2,
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function euthanizedIn24IsSummedIntoTheDenominator(): void
    {
        $result = Number::survivalRate((object) [
            'euthanized_in_24' => 2,
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(100.0, $result);
    }

    #[Test]
    public function diedAreDividedInTheCalculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ]);

        $this->assertSame(80.0, $result);
    }

    #[Test]
    public function diedAreAlwaysDividedInTheCalculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(80.0, $result);
    }

    #[Test]
    public function euthanizedAreDividedInTheCalculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ]);

        $this->assertSame(80.0, $result);
    }

    #[Test]
    public function euthanizedAreAlwaysDividedInTheCalculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(80.0, $result);
    }

    #[Test]
    public function itCalculatesTheSurvivalRateIncludingTheFirst24Hours(): void
    {
        $result = Number::survivalRate((object) [
            'relased' => 1,
            'pending' => 2,
            'transferred' => 3,
            'doa' => 4,
            'died_in_24' => 5,
            'euthanized_in_24' => 6,
            'total' => 21,
        ]);

        $this->assertSame(29.41, $result);
    }

    #[Test]
    public function itCalculatesTheSurvivalRateAfterTheFirst24Hours(): void
    {
        $result = Number::survivalRate((object) [
            'relased' => 1,
            'pending' => 2,
            'transferred' => 3,
            'doa' => 4,
            'died_in_24' => 5,
            'euthanized_in_24' => 6,
            'total' => 21,
        ], true);

        $this->assertSame(83.33, $result);
    }
}
