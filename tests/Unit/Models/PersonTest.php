<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class PersonTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

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
            'phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $person->phone_normalized);
        $this->assertEquals('+18085551234', $person->phone_e164);
        $this->assertEquals('(808) 555-1234', $person->phone_national);
    }

    #[Test]
    public function whenAPersonsPhoneNumberIsSavedItIsFormattedForMultipleUses(): void
    {
        $person = Person::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'phone' => '808-555-1234',
            'phone_normalized' => '8085551234',
            'phone_e164' => '+18085551234',
            'phone_national' => '(808) 555-1234',
        ]);
    }

    #[Test]
    public function whenAPersonsAlternatePhoneNumberIsAccessedItIsFormattedForTheirCountry(): void
    {
        $person = Person::factory()->create([
            'alternate_phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $person->alternate_phone_normalized);
        $this->assertEquals('+18085551234', $person->alternate_phone_e164);
        $this->assertEquals('(808) 555-1234', $person->alternate_phone_national);
    }

    #[Test]
    public function whenAPersonsAlternatePhoneNumberIsSavedItIsFormattedForTheirCountry(): void
    {
        $person = Person::factory()->create([
            'county' => 'US',
            'alternate_phone' => '808-555-1234',
        ]);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'alternate_phone' => '808-555-1234',
            'alternate_phone_normalized' => '8085551234',
            'alternate_phone_e164' => '+18085551234',
            'alternate_phone_national' => '(808) 555-1234',
        ]);
    }

    #[Test]
    public function whenAPhoneNumberDoesNotMatchACountryFormatItStillSavesToTheDatabase(): void
    {
        $person = Person::factory()->create([
            'phone' => '123',
            'alternate_phone' => '123',
        ]);

        $this->assertDatabaseHas('people', [
            'id' => $person->id,
            'phone' => '123',
            'phone_normalized' => '123',
            'phone_e164' => '123',
            'phone_national' => '123',
            'alternate_phone' => '123',
            'alternate_phone_normalized' => '123',
            'alternate_phone_e164' => '123',
            'alternate_phone_national' => '123',
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
