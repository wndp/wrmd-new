<?php

namespace Tests\Unit\Repositories;

use App\Repositories\AdministrativeDivision;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AdministrativeDivisionTest extends TestCase
{
    #[Test]
    public function itFormatsAnAddressInBlockFormat(): void
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

        $this->assertEquals('<p translate="no"> <span class="given-name">Jim Bob</span><br> <span class="organization">Foo Corporation</span><br> <span class="address-line1">123 Main st.</span><br> <span class="locality">Any Town</span>, <span class="administrative-area">CA</span> <span class="postal-code">12345</span><br> <span class="country">United States</span> </p>',
            $result
        );
    }

    #[Test]
    public function itFormatsAnAddressInInlineFormat(): void
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

    #[Test]
    public function itFormatsAPhoneNumber(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $result = $administrativeDivision->phoneNumber('9255551234');

        $this->assertEquals('(925) 555-1234', $result);
    }

    #[Test]
    public function itReturnsAnUnrecognizablePhoneNumberFormat(): void
    {
        $administrativeDivision = new AdministrativeDivision('en', 'US');

        $phoneNumber = '+10-123-4399 x 101';

        $result = $administrativeDivision->phoneNumber($phoneNumber);

        $this->assertEquals($phoneNumber, $result);
    }
}
