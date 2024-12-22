<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Number;
use Tests\TestCase;

final class SurvivalRateTest extends TestCase
{
    public function test_it_calculates_the_survival_rate_from_an_object_of_disposition_attributes(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    public function test_it_calculates_the_survival_rate_from_a_collection_of_objects_with_disposition_attributes(): void
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

    public function test_doa_is_subtracted_from_the_total(): void
    {
        $result = Number::survivalRate((object) [
            'doa' => 2,
            'pending' => 8,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    public function test_pending_is_summed_into_the_numerator(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    public function test_released_is_summed_into_the_numerator(): void
    {
        $result = Number::survivalRate((object) [
            'released' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    public function test_transferred_is_summed_into_the_numerator(): void
    {
        $result = Number::survivalRate((object) [
            'transferred' => 10,
            'total' => 10,
        ]);

        $this->assertSame(100.0, $result);
    }

    public function test_died_in24_is_summed_into_the_denominator(): void
    {
        $result = Number::survivalRate((object) [
            'died_in_24' => 2,
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(100.0, $result);
    }

    public function test_euthanized_in24_is_summed_into_the_denominator(): void
    {
        $result = Number::survivalRate((object) [
            'euthanized_in_24' => 2,
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(100.0, $result);
    }

    public function test_died_are_divided_in_the_calculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ]);

        $this->assertSame(80.0, $result);
    }

    public function test_died_are_always_divided_in_the_calculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(80.0, $result);
    }

    public function test_euthanized_are_divided_in_the_calculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ]);

        $this->assertSame(80.0, $result);
    }

    public function test_euthanized_are_always_divided_in_the_calculation(): void
    {
        $result = Number::survivalRate((object) [
            'pending' => 8,
            'total' => 10,
        ], true);

        $this->assertSame(80.0, $result);
    }

    public function test_it_calculates_the_survival_rate_including_the_first24_hours(): void
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

    public function test_it_calculates_the_survival_rate_after_the_first24_hours(): void
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
