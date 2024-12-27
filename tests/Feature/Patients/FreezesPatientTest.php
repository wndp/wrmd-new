<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Concerns\FreezesPatient;
use App\Domain\Patients\Patient;
use App\Domain\Patients\TreatmentLog;
use App\Domain\People\Person;
use App\Domain\Taxonomy\Taxon;
use Tests\TestCase;

final class FreezesPatientTest extends TestCase
{
    use FreezesPatient;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_if_the_frozen_status_is_being_updated_then_the_patient_can_update(): void
    {
        $patient = Patient::factory()->create();

        $patient->is_frozen = true;
        $patient->save();
        $this->assertTrue($patient->fresh()->isFrozen());

        $patient->is_frozen = false;
        $patient->save();
        $this->assertFalse($patient->fresh()->isFrozen());
    }

    public function test_it_knows_if_a_model_is_a_frozen_patient(): void
    {
        $this->assertFalse(Patient::isFrozenPatient(Patient::factory()->create()));
        $this->assertTrue(Patient::isFrozenPatient(Patient::factory()->create(['is_frozen' => true])));
    }

    public function test_it_knows_if_a_model_has_a_frozen_patient_relationship(): void
    {
        $this->assertFalse(self::hasFrozenPatientRelationship(
            TreatmentLog::factory()->create([
                'patient_id' => Patient::factory()->create()->id,
            ])
        ));

        $this->assertTrue(self::hasFrozenPatientRelationship(
            TreatmentLog::factory()->create([
                'patient_id' => Patient::factory()->create(['is_frozen' => true])->id,
            ])
        ));
    }

    public function test_it_knows_if_a_model_has_a_patients_collection_with_a_frozen_patient(): void
    {
        $rescuer = Person::factory()->create();
        $patient = Patient::factory()->create(['rescuer_id' => $rescuer->id]);

        $this->assertFalse(self::hasFrozenCollectionRelationship($rescuer));

        $patient->is_frozen = true;
        $patient->save();

        $this->assertTrue(self::hasFrozenCollectionRelationship($rescuer->fresh()));
    }
}
