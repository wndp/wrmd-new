<?php

namespace Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HelpersTest extends TestCase
{
    public function test_it_recognizes_a_numeric_year(): void
    {
        $result = is_year(2014);

        $this->assertTrue($result);
    }

    public function test_it_recognizes_a_string_year(): void
    {
        $result = is_year('2014');

        $this->assertTrue($result);
    }

    public function test_it_does_not_recognize_a_two_character_year(): void
    {
        $result = is_year('14');

        $this->assertFalse($result);
    }

    // #[Test]
    // public function itRecognizeATimeWithin24hrs(): void
    // {
    //     $twentyThreeHoursAgo = time() - 60 * 60 * 23;

    //     $result = is_within_24hr($twentyThreeHoursAgo);

    //     $this->assertTrue($result);
    // }

    // #[Test]
    // public function itRecognizeATimeAfter24hrs(): void
    // {
    //     $twentyFiveHoursAgo = time() - 60 * 60 * 25;

    //     $result = is_within_24hr($twentyFiveHoursAgo);

    //     $this->assertFalse($result);
    // }
}
