<?php

namespace Tests\Unit\Models;

use App\Models\OilWaterproofingAssessment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class OilWaterproofingAssessmentTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aOilWaterproofingAssessmentBelongsToAPatient(): void
    {
        $this->assertInstanceOf(Patient::class, OilWaterproofingAssessment::factory()->create()->patient);
    }

    #[Test]
    public function aOilWaterproofingAssessmentIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            OilWaterproofingAssessment::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifAnOilWaterproofingAssessmentsPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $assessments = OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id, 'examiner' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $assessments->patient->refresh();

        // Cant update
        $assessments->update(['examiner' => 'NEW']);
        $this->assertEquals('OLD', $assessments->fresh()->examiner);

        // Cant save
        $assessments->examiner = 'NEW';
        $assessments->save();
        $this->assertEquals('OLD', $assessments->fresh()->examiner);
    }

    #[Test]
    public function ifAnOilWaterproofingAssessmentsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $assessments = OilWaterproofingAssessment::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($assessments->exists);
    }

    #[Test]
    public function ifAOilWaterproofingAssessmentsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $assessments = OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $assessments->patient->refresh();

        $assessments->delete();
        $this->assertDatabaseHas('oil_waterproofing_assessments', ['id' => $assessments->id, 'deleted_at' => null]);
    }

    #[Test]
    public function whenAPatientIsReplicatedSoAreTheOilWaterproofingAssessments(): void
    {
        $patient = Patient::factory()->create();
        OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id]);
        OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, OilWaterproofingAssessment::where('patient_id', $patient->id)->get());
        $this->assertCount(2, OilWaterproofingAssessment::where('patient_id', $newPatient->id)->get());
    }
}
