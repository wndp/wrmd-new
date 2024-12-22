<?php

namespace Tests\Unit\Models;

use App\Models\OilProcessing;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class OilProcessingTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_an_oil_processing_belongs_to_a_patient(): void
    {
        $this->assertInstanceOf(Patient::class, OilProcessing::factory()->create()->patient);
    }

    public function test_an_oil_processing_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            OilProcessing::factory()->create(),
            'created'
        );
    }

    public function test_if_an_oil_processings_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $processing = OilProcessing::factory()->create(['patient_id' => $patient->id, 'comments' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $processing->patient->refresh();

        // Cant update
        $processing->update(['comments' => 'NEW']);
        $this->assertEquals('OLD', $processing->fresh()->comments);

        // Cant save
        $processing->comments = 'NEW';
        $processing->save();
        $this->assertEquals('OLD', $processing->fresh()->comments);
    }

    public function test_if_an_oil_processings_patient_is_locked_then_it_can_not_be_created(): void
    {
        $processing = OilProcessing::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($processing->exists);
    }

    public function test_if_an_oil_processings_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $processing = OilProcessing::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $processing->patient->refresh();

        $processing->delete();
        $this->assertDatabaseHas('oil_processings', ['id' => $processing->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_are_the_event_processings(): void
    {
        $patient = Patient::factory()->create();
        OilProcessing::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(1, OilProcessing::where('patient_id', $patient->id)->get());
        $this->assertCount(1, OilProcessing::where('patient_id', $newPatient->id)->get());
    }

    public function test_an_oil_processing_can_validate_ownership(): void
    {
        $team = Team::factory()->create();
        $processing = OilProcessing::factory()->create();

        $this->expectException(HttpResponseException::class);
        $processing->validateOwnership($team->id);
    }
}
