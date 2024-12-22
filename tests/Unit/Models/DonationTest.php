<?php

namespace Tests\Unit\Models;

use App\Models\Donation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class DonationTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_a_donation_has_an_appended_value_formatted_attribute(): void
    {
        $donation = Donation::factory()->create([
            'value' => 5000,
        ]);

        $this->assertEquals('$50.00', $donation->fresh()->value_for_humans);
    }

    public function test_a_donation_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Donation::factory()->create(),
            'created'
        );
    }
}
