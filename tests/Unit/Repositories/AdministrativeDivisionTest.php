<?php

namespace Tests\Unit\Repositories;

use App\Enums\PhoneFormat;
use App\Repositories\AdministrativeDivision;
use Tests\TestCase;

final class AdministrativeDivisionTest extends TestCase
{
    public function test_it_formats_an_address_in_block_format(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->blockAddress(
            organization: 'Foo Corporation',
            name: 'Jim Bob',
            addressLine1: '123 Main st.',
            city: 'Any Town',
            subdivision: 'CA',
            postalCode: '12345',
        );

        $this->assertEquals(
            '<p translate="no"> <span class="given-name">Jim Bob</span><br> <span class="organization">Foo Corporation</span><br> <span class="address-line1">123 Main st.</span><br> <span class="locality">Any Town</span>, <span class="administrative-area">CA</span> <span class="postal-code">12345</span><br> <span class="country">United States</span> </p>',
            $result
        );
    }

    public function test_it_formats_an_address_in_inline_format(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->inlineAddress(
            organization: 'Foo Corporation',
            name: 'Jim Bob',
            addressLine1: '123 Main st.',
            city: 'Any Town',
            subdivision: 'CA',
            postalCode: '12345',
        );

        $this->assertEquals('Jim Bob Foo Corporation 123 Main st. Any Town, CA 12345 United States', $result);
    }

    public function test_it_formats_a_phone_number_as_e164(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->phoneNumber('9255551234', format: PhoneFormat::E164);

        $this->assertEquals('+19255551234', $result);
    }

    public function test_it_formats_a_phone_number_into_the_national_pattern(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->phoneNumber('9255551234', format: PhoneFormat::NATIONAL);

        $this->assertEquals('(925) 555-1234', $result);
    }

    public function test_it_formats_a_phone_number_into_a_normalized_pattern(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->phoneNumber('(925) 555-1234', format: PhoneFormat::NORMALIZED);

        $this->assertEquals('9255551234', $result);
    }

    public function test_a_phone_number_that_is_not_valid_formats_into_a_normalized_pattern(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->phoneNumber('1*234!');

        $this->assertEquals('1234', $result);
    }
}
