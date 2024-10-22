<?php

namespace Tests\Unit\Models;

use App\Domain\Taxonomy\Taxon;
use App\Models\Patient;
use App\Models\Person;
use App\Repositories\AdministrativeDivision;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class PersonTest extends TestCase
{
    use RefreshDatabase;
    use Assertions;

    #[Test]
    public function aPersonIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Person::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function whenAPersonsPhoneNumberIsAccessedItIsFormattedForTheirCountry(): void
    {
        $person = Person::factory()->create([
            'phone' => '808*555-1234',
        ]);

        $this->assertEquals('(808) 555-1234', $person->phone);
    }

    #[Test]
    public function whenAPersonsPhoneNumberIsSavedItIsFormattedForTheirCountry(): void
    {
        $person = Person::factory()->create([
            'phone' => '808*555-1234',
        ]);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'phone' => '8085551234',
        ]);
    }

    #[Test]
    public function whenAPersonsAltPhoneNumberIsAccessedItIsFormattedForTheirCountry(): void
    {
        $person = Person::factory()->create([
            'alternate_phone' => '808*555-1234',
        ]);

        $this->assertEquals('(808) 555-1234', $person->alternate_phone);
    }

    #[Test]
    public function whenAPersonsAltPhoneNumberIsSavedItIsFormattedForTheirCountry(): void
    {
        $person = Person::factory()->create([
            'alternate_phone' => '808*555-1234',
        ]);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'alternate_phone' => '8085551234',
        ]);
    }

    #[Test]
    public function ifARescuersPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $rescuer = Person::factory()->create(['first_name' => 'OLD']);
        $patient = Patient::factory()->create(['rescuer_id' => $rescuer->id, 'locked_at' => Carbon::now()]);

        // Cant update
        $rescuer->update(['first_name' => 'NEW']);
        $this->assertEquals('OLD', $rescuer->fresh()->first_name);

        // Cant save
        $rescuer->first_name = 'NEW';
        $rescuer->save();
        $this->assertEquals('OLD', $rescuer->fresh()->first_name);
    }

    #[Test]
    public function ifARescuersPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $rescuer = Person::factory()->create(['first_name' => 'OLD']);
        $patient = Patient::factory()->create(['rescuer_id' => $rescuer->id, 'locked_at' => Carbon::now()]);

        $rescuer->delete();
        $this->assertDatabaseHas('people', ['id' => $rescuer->id]);
    }
}
