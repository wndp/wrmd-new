<?php

namespace Tests\Unit\Models;

use App\Actions\GetPatientWeights;
use App\Models\Morphometric;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesUiBehavior;

final class MorphometricTest extends TestCase
{
    use Assertions;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_a_morphometric_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Morphometric::factory()->create(),
            'created'
        );
    }

    public function test_if_a_morphometrics_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $morphometric = Morphometric::factory()->create(['patient_id' => $patient->id, 'remarks' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $morphometric->patient->refresh();

        // Cant update
        $morphometric->update(['remarks' => 'NEW']);
        $this->assertEquals('OLD', $morphometric->fresh()->remarks);

        // Cant save
        $morphometric->remarks = 'NEW';
        $morphometric->save();
        $this->assertEquals('OLD', $morphometric->fresh()->remarks);
    }

    public function test_if_a_morphometrics_patient_is_locked_then_it_can_not_be_created(): void
    {
        $morphometric = Morphometric::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($morphometric->exists);
    }

    public function test_if_a_morphometrics_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $morphometric = Morphometric::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $morphometric->patient->refresh();

        $morphometric->delete();
        $this->assertDatabaseHas('morphometrics', ['id' => $morphometric->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_is_the_morphometric(): void
    {
        $patient = Patient::factory()->create();
        Morphometric::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(1, Morphometric::where('patient_id', $patient->id)->get());
        $this->assertCount(1, Morphometric::where('patient_id', $newPatient->id)->get());
    }

    public function test_it_filters_the_morphometric_weight_into_the_patient_weights_collection(): void
    {
        [$kgWeightId, $gWeightId] = $this->weightUnits();

        $patient = Patient::factory()->create();
        Morphometric::factory()->create([
            'patient_id' => $patient->id,
            'weight' => 123.45,
            'measured_at' => '2019-03-23 09:00:00',
        ]);

        $weightObj = GetPatientWeights::run($patient)->getLastWeight();

        $this->assertEquals('Morphometrics', $weightObj->type);
        $this->assertEquals(123.45, $weightObj->weight);
        $this->assertEquals($gWeightId, $weightObj->unit_id);
        $this->assertEquals('2019-03-23', $weightObj->weighed_at_date);
    }
}
