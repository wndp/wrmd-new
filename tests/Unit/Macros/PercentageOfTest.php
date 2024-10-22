<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Number;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PercentageOfTest extends TestCase
{
    #[Test]
    public function itCalculatesThePercentageOf(): void
    {
        $result = Number::percentageOf(3, 10);
        $this->assertEquals(30.0, $result);

        $result = Number::percentageOf(3, 14);
        $this->assertEquals(21.43, $result);
    }
}
