<?php

namespace Tests\Unit\Models;

use App\Models\Banding;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class BandingTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_a_banding_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Banding::factory()->create(),
            'created'
        );
    }

    public function test_if_a_bandings_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $banding = Banding::factory()->create(['patient_id' => $patient->id, 'band_number' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $banding->patient->refresh();

        // Cant update
        $banding->update(['band_number' => 'NEW']);
        $this->assertEquals('OLD', $banding->fresh()->band_number);

        // Cant save
        $banding->band_number = 'NEW';
        $banding->save();
        $this->assertEquals('OLD', $banding->fresh()->band_number);
    }

    public function test_if_a_bandings_patient_is_locked_then_it_can_not_be_created(): void
    {
        $banding = Banding::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($banding->exists);
    }

    public function test_if_a_bandings_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $banding = Banding::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $banding->patient->refresh();

        $banding->delete();
        $this->assertDatabaseHas('bandings', ['id' => $banding->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_is_the_banding(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        Banding::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(1, Banding::where('patient_id', $patient->id)->get());
        $this->assertCount(1, Banding::where('patient_id', $newPatient->id)->get());
    }
}
