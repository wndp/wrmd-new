<?php

namespace Tests\Unit\Support;

use App\Macros\Collection;
use Tests\TestCase;

final class CollectionMacrosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Collection::applyMacros();
    }

    public function testSliceBefore(): void
    {
        $items = collect([32, 24, 56, 72, 19, 61, 3, 11]);

        $expected = [
            [32, 24],
            [56],
            [72, 19],
            [61, 3, 11],
        ];

        $result = $items->sliceBefore(function ($number) {
            return $number > 50;
        });

        $this->assertSame($expected, $result->toArray());
    }

    public function testEachCons(): void
    {
        $items = collect([1, 2, 3, 4, 5, 6]);

        $expected = [
            [1, 2],
            [2, 3],
            [3, 4],
            [4, 5],
            [5, 6],
        ];

        $result = $items->eachCons(2);

        $this->assertSame($expected, $result->toArray());
    }

    public function testChunkBy(): void
    {
        $items = collect(['A', 'A', 'A', 'B', 'A', 'A', 'B', 'B', 'A']);

        $expected = [
            ['A', 'A', 'A'],
            ['B'],
            ['A', 'A'],
            ['B', 'B'],
            ['A'],
        ];

        $result = $items->chunkBy(function ($item) {
            return $item === 'A';
        });

        $this->assertSame($expected, $result->toArray());
    }

    public function testFlop(): void
    {
        $items = collect([
            'apple' => [
                'fruit',
                'red',
            ],
            'lettuce' => [
                'vegetable',
                'green',
            ],
            'tomato' => [
                'fruit',
                'red',
            ],
        ]);

        $expected = [
            'fruit' => [
                'apple',
                'tomato',
            ],
            'red' => [
                'apple',
                'tomato',
            ],
            'vegetable' => [
                'lettuce',
            ],
            'green' => [
                'lettuce',
            ],
        ];

        $result = $items->flipFlop();

        $this->assertSame($expected, $result->toArray());
    }
}
