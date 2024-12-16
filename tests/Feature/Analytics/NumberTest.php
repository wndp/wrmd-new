<?php

namespace Tests\Feature\Analytics;

use App\Analytics\AnalyticFilters;
use App\Analytics\Contracts\Number;
use App\Models\Team;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NumberTest extends TestCase
{
    #[Test]
    public function itCalculatesThePercentageDifferenceBetweenEqualNumbers(): void
    {
        $this->anonymousNumber->calculatePercentageDifference(1, 1);

        $this->assertEquals('same', $this->anonymousNumber->change);
        $this->assertEquals(0.0, $this->anonymousNumber->difference);
    }

    #[Test]
    public function itCalculatesThePercentageDifferenceBetweenAnDecreasedNumber(): void
    {
        $this->anonymousNumber->calculatePercentageDifference(1, 2);

        $this->assertEquals('down', $this->anonymousNumber->change);
        $this->assertEquals(50, $this->anonymousNumber->difference);
    }

    #[Test]
    public function itCalculatesThePercentageDifferenceBetweenAnIncreasedNumber(): void
    {
        $this->anonymousNumber->calculatePercentageDifference(3, 1);

        $this->assertEquals('up', $this->anonymousNumber->change);
        $this->assertEquals(66.7, $this->anonymousNumber->difference);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->anonymousNumber = new class(Team::factory()->make(), new AnalyticFilters) extends Number
        {
            public function compute() {}

            public function calculatePercentageDifference($a, $b)
            {
                return parent::calculatePercentageDifference($a, $b);
            }
        };
    }
}
