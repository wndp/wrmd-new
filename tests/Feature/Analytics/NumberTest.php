<?php

namespace Tests\Feature\Analytics;

use App\Analytics\AnalyticFilters;
use App\Analytics\Contracts\Number;
use App\Models\Team;
use Tests\TestCase;

final class NumberTest extends TestCase
{
    public function test_it_calculates_the_percentage_difference_between_equal_numbers(): void
    {
        $this->anonymousNumber->calculatePercentageDifference(1, 1);

        $this->assertEquals('same', $this->anonymousNumber->change);
        $this->assertEquals(0.0, $this->anonymousNumber->difference);
    }

    public function test_it_calculates_the_percentage_difference_between_an_decreased_number(): void
    {
        $this->anonymousNumber->calculatePercentageDifference(1, 2);

        $this->assertEquals('down', $this->anonymousNumber->change);
        $this->assertEquals(50, $this->anonymousNumber->difference);
    }

    public function test_it_calculates_the_percentage_difference_between_an_increased_number(): void
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
