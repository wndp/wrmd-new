<?php

namespace Tests\Unit\Support;

use App\Domain\Accounts\Account;
use Carbon\Carbon;
use Illuminate\Support\Fluent;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class FormattingHelpersTest extends TestCase
{
    //use AssistsWithCases;
    //use AssistsWithAuthentication;

    // #[Test]
    // public function itFormatsAnEmptyField(): void
    // {
    //     $result = field_is_empty('foo');
    //     $this->assertEquals("(`foo` is null or lower(`foo`) = 'null' or trim(`foo`) = '')", $result);

    //     $result = field_is_empty('foo.bar');
    //     $this->assertEquals("(`foo`.`bar` is null or lower(`foo`.`bar`) = 'null' or trim(`foo`.`bar`) = '')", $result);
    // }

    // #[Test]
    // public function itFormatsANotEmptyField(): void
    // {
    //     $result = field_is_not_empty('foo');
    //     $this->assertEquals("(trim(ifnull(`foo`,'')) <> '')", $result);

    //     $result = field_is_not_empty('foo.bar');
    //     $this->assertEquals("(trim(ifnull(`foo`.`bar`,'')) <> '')", $result);
    // }

    // #[Test]
    // public function itFormatsAnEmptyDate(): void
    // {
    //     $result = format_date('');

    //     $this->assertNull($result);
    // }

    // #[Test]
    // public function itFormatsACarbonDate(): void
    // {
    //     $carbon = \Carbon\Carbon::create(2015, 1, 6);

    //     $result = format_date($carbon);

    //     $this->assertEquals('1/6/2015', $result);
    // }

    // #[Test]
    // public function itFormatsACarbonDateAccordingToAFormatString(): void
    // {
    //     $carbon = \Carbon\Carbon::create(2015, 1, 6);

    //     $result = format_date($carbon, 'D d, M Y');

    //     $this->assertEquals('Tue 06, Jan 2015', $result);
    // }

    // #[Test]
    // public function itFormatsA0000Date(): void
    // {
    //     $result = format_date('0000-00-00', 'n/j/Y', 'alternate');

    //     $this->assertEquals('alternate', $result);
    // }

    // #[Test]
    // public function itFormatsASqlDate(): void
    // {
    //     $result = format_date('2015-01-06 01:00:00');

    //     $this->assertEquals('1/6/2015', $result);
    // }

    // #[Test]
    // public function itFormatsAnAlphaDate(): void
    // {
    //     $result = format_date('now');

    //     $this->assertEquals(date('n/j/Y'), $result);
    // }

    // #[Test]
    // public function itFormatsAnTimestampDate(): void
    // {
    //     $result = format_date(1468092176);

    //     $this->assertEquals('7/9/2016', $result);
    // }

    // #[Test]
    // public function itCantFormatABogusDate(): void
    // {
    //     $result = format_date('foo bar');

    //     $this->assertNull($result);
    // }

    #[Test]
    public function itFormatsATimestampWithoutTimes(): void
    {
        $this->setSetting(
            \App\Domain\Accounts\Account::factory()->create(),
            'timezone',
            'America/Los_Angeles'
        );

        $expected = '1980-01-28 00:00:00';
        $result = format_datetime('1/28/80');

        $this->assertEquals($expected, $result);
    }

    // #[Test]
    // public function itFormatsTheDispositions(): void
    // {
    //     $result = format_disposition('Pending');
    //     $this->assertEquals('Pending', $result);

    //     $result = format_disposition('Euthanized +24hr');
    //     $this->assertEquals('Euthanized', $result);

    //     $result = format_disposition('Died +24hr');
    //     $this->assertEquals('Died', $result);
    // }

    #[Test]
    public function itIntelligentlyFormats(): void
    {
        $this->setSetting(
            Account::factory()->create(),
            'date_format',
            'n/j/Y'
        );

        $object = new Fluent([
            'found_at' => Carbon::now(),
            'keywords' => 'foo',
        ]);

        $result = format_intelligently('area', $object);
        $this->assertEmpty($result);

        $result = format_intelligently('found_at', $object);
        $this->assertEquals(Carbon::now()->format('n/j/Y'), $result);

        $result = format_intelligently('keywords', $object);
        $this->assertEquals('foo', $result);
    }
}
