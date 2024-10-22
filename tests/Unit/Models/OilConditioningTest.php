<?php

namespace Tests\Unit\Models;

use App\Models\OilConditioning;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class OilConditioningTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aOilConditioningBelongsToAPatient(): void
    {
        $this->assertInstanceOf(Patient::class, OilConditioning::factory()->create()->patient);
    }

    #[Test]
    public function aOilConditioningIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            OilConditioning::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifAnOilConditioningsPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $conditioning = OilConditioning::factory()->create(['patient_id' => $patient->id, 'examiner' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $conditioning->patient->refresh();

        // Cant update
        $conditioning->update(['examiner' => 'NEW']);
        $this->assertEquals('OLD', $conditioning->fresh()->examiner);

        // Cant save
        $conditioning->examiner = 'NEW';
        $conditioning->save();
        $this->assertEquals('OLD', $conditioning->fresh()->examiner);
    }

    #[Test]
    public function ifAnOilConditioningsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $conditioning = OilConditioning::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($conditioning->exists);
    }

    #[Test]
    public function ifAOilConditioningsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $conditioning = OilConditioning::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $conditioning->patient->refresh();

        $conditioning->delete();
        $this->assertDatabaseHas('oil_conditionings', ['id' => $conditioning->id, 'deleted_at' => null]);
    }

    #[Test]
    public function whenAPatientIsReplicatedSoAreTheEventConditionings(): void
    {
        $patient = Patient::factory()->create();
        OilConditioning::factory()->create(['patient_id' => $patient->id]);
        OilConditioning::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, OilConditioning::where('patient_id', $patient->id)->get());
        $this->assertCount(2, OilConditioning::where('patient_id', $newPatient->id)->get());
    }
}
