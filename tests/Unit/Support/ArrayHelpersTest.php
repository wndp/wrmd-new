<?php

namespace Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ArrayHelpersTest extends TestCase
{
    #[Test]
    public function itSuffixesAStringToAnArraysValues(): void
    {
        // Give that I have a string and an array
        $string = '-string';
        $array = ['foo', 'bar'];

        // When I try to append the sting to the array values
        $result = array_suffix($string, $array);

        // Then the returned array's values should have the string appended to all the original arrays values
        $this->assertCount(2, $result);
        $this->assertEquals('foo-string', $result[0]);
        $this->assertEquals('bar-string', $result[1]);
    }

    #[Test]
    public function itUsesAnArraysValuesToMakeANewArrayWithTheEqualValues(): void
    {
        $array = ['a' => 'foo', 'b' => 'bar'];
        $emptyFirstOption = true;

        $result = value_as_key($array, $emptyFirstOption);

        $this->assertEquals(['' => '', 'foo' => 'foo', 'bar' => 'bar'], $result);
        $this->assertEquals(array_values($result)[0], '');
    }

    #[Test]
    public function itUsesAMultidimensionalArraysValuesToMakeANewArrayWithTheEqualValues(): void
    {
        $array = [['a' => 'foo', 'b' => 'bar'], 'key' => ['a' => 'bing', 'b' => 'bang']];
        $emptyFirstOption = true;

        $result = value_as_key($array, $emptyFirstOption);

        $this->assertEquals(['' => '', ['foo' => 'foo', 'bar' => 'bar'], 'key' => ['bing' => 'bing', 'bang' => 'bang']], $result);
        $this->assertEquals(array_values($result)[0], '');
    }

    #[Test]
    public function itUsesAnArraysKeysToMakeANewArrayWithTheEqualValues(): void
    {
        $array = ['a' => 'foo', 'b' => 'bar'];
        $emptyFirstOption = true;

        $result = key_as_value($array, $emptyFirstOption);

        $this->assertEquals(['' => '', 'a' => 'a', 'b' => 'b'], $result);
        $this->assertEquals(array_values($result)[0], '');
    }

    #[Test]
    public function itDeletesTheValuesFromAnArray(): void
    {
        $array = ['a' => 'foo', 'b' => 'bar'];

        $result = array_clean($array);

        $this->assertNull($result['a']);
        $this->assertNull($result['b']);
    }

    #[Test]
    public function itRemovesEmptyValuesFromAnArray(): void
    {
        $array = [
            0 => 'foo',
            1 => ' ',
            2 => null,
            3 => false,
            4 => 'bar',
            5 => 0,
            6 => '0',
        ];

        $result = array_remove($array);

        $this->assertEquals('foo', $result[0]);
        $this->assertEquals('bar', $result[4]);
        $this->assertEquals(0, $result[5]);
        $this->assertEquals('0', $result[6]);

        $this->assertArrayNotHasKey(1, $result);
        $this->assertArrayNotHasKey(2, $result);
        $this->assertArrayNotHasKey(3, $result);
    }

    #[Test]
    public function itRemovesDuplicatesAndResetsKeys(): void
    {
        $array = [0 => 'foo', 1 => 'foo', 2 => 'bar'];

        $result = array_unique_reset($array);

        $this->assertCount(2, $result);
        $this->assertArrayNotHasKey(2, $result);
    }

    #[Test]
    public function itAssertsThatAnArrayContainsAllTheValuesOfAnotherArray(): void
    {
        $result1 = in_array_multiple(
            ['a', 'b', 'c'],
            ['a', 'b', 'c', 'd']
        );

        $this->assertTrue($result1);

        $result2 = in_array_multiple(
            ['a', 'b', 'c'],
            ['a', 'b']
        );

        $this->assertFalse($result2);
    }

    #[Test]
    public function itTranslatesAndMergesTheTerminologyForAnUnknownKey(): void
    {
        app('events')->listen('terminology.foo', function () {
            return ['bar'];
        });

        $this->assertSame(['terminology.foo', 'bar'], trans_terminology('foo'));
    }
}
