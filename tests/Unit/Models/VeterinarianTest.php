<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class VeterinarianTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_veterinarian_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Veterinarian::factory()->create(),
            'created'
        );
    }

    public function test_a_veterinarian_belongs_to_a_team(): void
    {
        $veterinarian = Veterinarian::factory()->create();

        $this->assertInstanceOf(Team::class, $veterinarian->team);
    }

    public function test_a_veterinarian_can_belong_to_a_user(): void
    {
        $veterinarian = Veterinarian::factory()->create();
        $this->assertNull($veterinarian->user);

        $veterinarian->user()->associate(User::factory()->create());

        $this->assertInstanceOf(User::class, $veterinarian->user);
    }

    public function test_when_a_veterinarians_phone_number_is_accessed_it_is_formatted_for_their_country(): void
    {
        $veterinarian = Veterinarian::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $veterinarian->phone_normalized);
        $this->assertEquals('+18085551234', $veterinarian->phone_e164);
        $this->assertEquals('(808) 555-1234', $veterinarian->phone_national);
    }

    public function test_when_a_veterinarians_phone_number_is_saved_it_is_formatted_for_its_country(): void
    {
        $veterinarian = Veterinarian::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertDatabaseHas('veterinarians', [
            'id' => $veterinarian->id,
            'phone' => '808-555-1234',
            'phone_normalized' => '8085551234',
            'phone_e164' => '+18085551234',
            'phone_national' => '(808) 555-1234',
        ]);
    }

    public function test_when_a_phone_number_does_not_match_a_country_format_it_still_saves_to_the_database(): void
    {
        $veterinarian = Veterinarian::factory()->create([
            'phone' => '123',
        ]);

        $this->assertDatabaseHas('veterinarians', [
            'id' => $veterinarian->id,
            'phone' => '123',
            'phone_normalized' => '123',
            'phone_e164' => '123',
            'phone_national' => '123',
        ]);
    }
}
