<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PrefixTest extends TestCase
{
    #[Test]
    public function itPrefixesAStringToAnArraysValues(): void
    {
        $string = 'string-';
        $array = ['foo', 'bar'];

        $result = Arr::prefix($array, $string);

        $this->assertCount(2, $result);
        $this->assertEquals('string-foo', $result[0]);
        $this->assertEquals('string-bar', $result[1]);
    }
}
