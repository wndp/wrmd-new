<?php

namespace Tests\Unit\Models;

use App\Models\OilWaterproofingAssessment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class OilWaterproofingAssessmentTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_oil_waterproofing_assessment_belongs_to_a_patient(): void
    {
        $this->assertInstanceOf(Patient::class, OilWaterproofingAssessment::factory()->create()->patient);
    }

    public function test_a_oil_waterproofing_assessment_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            OilWaterproofingAssessment::factory()->create(),
            'created'
        );
    }

    public function test_if_an_oil_waterproofing_assessments_patient_is_locked_then_it_can_not_be_updated(): void
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

    public function test_if_an_oil_waterproofing_assessments_patient_is_locked_then_it_can_not_be_created(): void
    {
        $assessments = OilWaterproofingAssessment::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($assessments->exists);
    }

    public function test_if_a_oil_waterproofing_assessments_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $assessments = OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $assessments->patient->refresh();

        $assessments->delete();
        $this->assertDatabaseHas('oil_waterproofing_assessments', ['id' => $assessments->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_are_the_oil_waterproofing_assessments(): void
    {
        $patient = Patient::factory()->create();
        OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id]);
        OilWaterproofingAssessment::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, OilWaterproofingAssessment::where('patient_id', $patient->id)->get());
        $this->assertCount(2, OilWaterproofingAssessment::where('patient_id', $newPatient->id)->get());
    }
}
