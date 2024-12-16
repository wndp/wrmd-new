<?php

namespace Tests\Unit\Models;

use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class DonationTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function aDonationHasAnAppendedValueFormattedAttribute(): void
    {
        $donation = Donation::factory()->create([
            'value' => 5000,
        ]);

        $this->assertEquals('$50.00', $donation->fresh()->value_for_humans);
    }

    #[Test]
    public function aDonationIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Donation::factory()->create(),
            'created'
        );
    }
}
