<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\User;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class VeterinarianTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aVeterinarianIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Veterinarian::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function aVeterinarianBelongsToATeam(): void
    {
        $veterinarian = Veterinarian::factory()->create();

        $this->assertInstanceOf(Team::class, $veterinarian->team);
    }

    #[Test]
    public function aVeterinarianCanBelongToAUser(): void
    {
        $veterinarian = Veterinarian::factory()->create();
        $this->assertNull($veterinarian->user);

        $veterinarian->user()->associate(User::factory()->create());

        $this->assertInstanceOf(User::class, $veterinarian->user);
    }

    #[Test]
    public function whenAVeterinariansPhoneNumberIsAccessedItIsFormattedForTheirCountry(): void
    {
        $veterinarian = Veterinarian::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $veterinarian->phone_normalized);
        $this->assertEquals('+18085551234', $veterinarian->phone_e164);
        $this->assertEquals('(808) 555-1234', $veterinarian->phone_national);
    }

    #[Test]
    public function whenAVeterinariansPhoneNumberIsSavedItIsFormattedForItsCountry(): void
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

    #[Test]
    public function whenAPhoneNumberDoesNotMatchACountryFormatItStillSavesToTheDatabase(): void
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
