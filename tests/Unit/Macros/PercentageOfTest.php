<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Number;
use Tests\TestCase;

final class PercentageOfTest extends TestCase
{
    public function test_it_calculates_the_percentage_of(): void
    {
        $result = Number::percentageOf(3, 10);
        $this->assertEquals(30.0, $result);

        $result = Number::percentageOf(3, 14);
        $this->assertEquals(21.43, $result);
    }
}
