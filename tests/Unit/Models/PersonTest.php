<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class PersonTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_person_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Person::factory()->create(),
            'created'
        );
    }

    public function test_when_a_persons_phone_number_is_accessed_it_is_formatted_for_their_country(): void
    {
        $person = Person::factory()->create([
            'phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $person->phone_normalized);
        $this->assertEquals('+18085551234', $person->phone_e164);
        $this->assertEquals('(808) 555-1234', $person->phone_national);
    }

    public function test_when_a_persons_phone_number_is_saved_it_is_formatted_for_multiple_uses(): void
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

    public function test_when_a_persons_alternate_phone_number_is_accessed_it_is_formatted_for_their_country(): void
    {
        $person = Person::factory()->create([
            'alternate_phone' => '808-555-1234',
        ]);

        $this->assertEquals('8085551234', $person->alternate_phone_normalized);
        $this->assertEquals('+18085551234', $person->alternate_phone_e164);
        $this->assertEquals('(808) 555-1234', $person->alternate_phone_national);
    }

    public function test_when_a_persons_alternate_phone_number_is_saved_it_is_formatted_for_their_country(): void
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

    public function test_when_a_phone_number_does_not_match_a_country_format_it_still_saves_to_the_database(): void
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

    public function test_if_a_rescuers_patient_is_locked_then_it_can_not_be_updated(): void
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

    public function test_if_a_rescuers_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $rescuer = Person::factory()->create(['first_name' => 'OLD']);
        $patient = Patient::factory()->create(['rescuer_id' => $rescuer->id, 'locked_at' => Carbon::now()]);

        $rescuer->delete();
        $this->assertDatabaseHas('people', ['id' => $rescuer->id]);
    }
}
