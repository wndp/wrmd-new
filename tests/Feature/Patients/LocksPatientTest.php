<?php

namespace Tests\Feature\Patients;

use App\Concerns\LocksPatient;
use App\Models\CareLog;
use App\Models\Patient;
use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LocksPatientTest extends TestCase
{
    use LocksPatient;
    use RefreshDatabase;

    public function test_if_the_locked_status_is_being_updated_then_the_patient_can_update(): void
    {
        $patient = Patient::factory()->create();

        $patient->locked_at = Carbon::now();
        $patient->save();
        $this->assertNotNull($patient->fresh()->locked_at);

        $patient->locked_at = null;
        $patient->save();
        $this->assertNull($patient->fresh()->locked_at);
    }

    public function test_it_knows_if_a_model_is_a_locked_patient(): void
    {
        $this->assertFalse(Patient::isLockedPatient(Patient::factory()->create()));
        $this->assertTrue(Patient::isLockedPatient(Patient::factory()->create(['locked_at' => Carbon::now()])));
    }

    public function test_it_knows_if_a_model_has_a_locked_patient_relationship(): void
    {
        $this->assertFalse(self::hasLockedPatientRelationship(
            CareLog::factory()->create([
                'patient_id' => Patient::factory()->create()->id,
            ])
        ));

        $this->assertTrue(self::hasLockedPatientRelationship(
            CareLog::factory()->create([
                'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
            ])
        ));
    }

    public function test_it_knows_if_a_model_has_a_patients_collection_with_a_locked_patient(): void
    {
        $rescuer = Person::factory()->create();
        $patient = Patient::factory()->create(['rescuer_id' => $rescuer->id]);

        $this->assertFalse(self::hasLockedCollectionRelationship($rescuer));

        $patient->locked_at = Carbon::now();
        $patient->save();

        $this->assertTrue(self::hasLockedCollectionRelationship($rescuer->fresh()));
    }
}
