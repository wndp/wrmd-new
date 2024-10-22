<?php

namespace Tests\Unit\Support;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HelpersTest extends TestCase
{
    #[Test]
    public function itRecognizesANumericYear(): void
    {
        $result = is_year(2014);

        $this->assertTrue($result);
    }

    #[Test]
    public function itRecognizesAStringYear(): void
    {
        $result = is_year('2014');

        $this->assertTrue($result);
    }

    #[Test]
    public function itDoesNotRecognizeATwoCharacterYear(): void
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
