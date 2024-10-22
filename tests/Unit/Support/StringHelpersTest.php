<?php

namespace Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class StringHelpersTest extends TestCase
{
    #[Test]
    public function strUnitReturnsNullWhenTheNeedleIsAUnit(): void
    {
        $this->assertNull(str_unit('foo_unit'));
    }

    #[Test]
    public function strUnitReturnsDefaultWhenTheNeedleDoesNotExistInTheHaystackOrHaveAUnit(): void
    {
        $this->assertEquals('default', str_unit('foo', [], 'default'));
    }

    #[Test]
    public function strUnitReturnsTheNeedlesValueWhenTheNeedleExistsInTheHaystack(): void
    {
        $this->assertEquals('bar', str_unit('foo', ['foo' => 'bar']));
    }

    #[Test]
    public function strUnitReturnsTheStringWithTheUnitWhenTheStringHasAUnit(): void
    {
        $this->assertEquals('5%', str_unit('foo', ['foo' => 5, 'foo_unit' => '%']));
    }

    #[Test]
    public function itGetsTheInitialsFromASting(): void
    {
        $result = str_initials('Devin frisk doMBrowSki');

        $this->assertEquals('DFD', $result);
    }
}
