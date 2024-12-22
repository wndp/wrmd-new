<?php

namespace Tests\Unit\Macros;

use Illuminate\Support\Arr;
use Tests\TestCase;

final class PrefixTest extends TestCase
{
    public function test_it_prefixes_a_string_to_an_arrays_values(): void
    {
        $string = 'string-';
        $array = ['foo', 'bar'];

        $result = Arr::prefix($array, $string);

        $this->assertCount(2, $result);
        $this->assertEquals('string-foo', $result[0]);
        $this->assertEquals('string-bar', $result[1]);
    }
}
