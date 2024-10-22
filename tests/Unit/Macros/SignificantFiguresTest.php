<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Number;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SignificantFiguresTest extends TestCase
{
    #[Test]
    public function itFormatsANumberToSignificantFigures(): void
    {
        $result = Number::significantFigures(12.034, 3);
        $this->assertEquals(12.0, $result);

        $result = Number::significantFigures(-12.034, 3);
        $this->assertEquals(-12.0, $result);
    }
}
