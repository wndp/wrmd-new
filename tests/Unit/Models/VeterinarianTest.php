<?php

namespace Tests\Unit\Models;

use App\Models\Team;
use App\Models\User;
use App\Models\Veterinarian;
use App\Repositories\AdministrativeDivision;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class VeterinarianTest extends TestCase
{
    use RefreshDatabase;
    use Assertions;

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
    public function whenAnVeterinariansPhoneIsAccessedItIsFormattedForItsCountry(): void
    {
        $veterinarian = Veterinarian::factory()->create([
            'phone' => '808*555-1234',
        ]);

        $this->assertEquals('(808) 555-1234', $veterinarian->phone);
    }

    #[Test]
    public function whenAnVeterinariansPhoneIsSavedItIsFormattedForItsCountry(): void
    {
        $veterinarian = Veterinarian::factory()->create([
            'phone' => '808*555-1234',
        ]);

        $this->assertDatabaseHas('veterinarians', [
            'id' => $veterinarian->id,
            'phone' => '8085551234',
        ]);
    }
}
