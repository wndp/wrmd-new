<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Number;
use Tests\TestCase;

final class SignificantFiguresTest extends TestCase
{
    public function test_it_formats_a_number_to_significant_figures(): void
    {
        $result = Number::significantFigures(12.034, 3);
        $this->assertEquals(12.0, $result);

        $result = Number::significantFigures(-12.034, 3);
        $this->assertEquals(-12.0, $result);
    }
}
