<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use App\Models\Team;
use App\Models\WildlifeRecoveryPatient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class WildlifeRecoveryPatientTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_wildlife_recovery_patient_belongs_to_a_patient(): void
    {
        $this->assertInstanceOf(Patient::class, WildlifeRecoveryPatient::factory()->create([
            'patient_id' => Patient::factory(),
        ])->patient);
    }

    public function test_a_wildlife_recovery_patient_belongs_to_a_team(): void
    {
        $this->assertInstanceOf(Team::class, WildlifeRecoveryPatient::factory()->create()->team);
    }

    public function test_a_wildlife_recovery_patient_can_be_found_by_its_qr_code_hash(): void
    {
        WildlifeRecoveryPatient::factory()->create([
            'qr_code' => 'https://wr.md/abc123',
        ]);

        $wr = WildlifeRecoveryPatient::findByHash('abc123');

        $this->assertInstanceOf(WildlifeRecoveryPatient::class, $wr);
        $this->assertEquals('https://wr.md/abc123', $wr->qr_code);
    }

    public function test_a_wildlife_recovery_patient_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            WildlifeRecoveryPatient::factory()->create(),
            'created'
        );
    }

    public function test_if_a_wildlife_recovery_patients_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $wrPatient = WildlifeRecoveryPatient::factory()->create(['patient_id' => $patient->id, 'surveyname' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $wrPatient->patient->refresh();

        // Cant update
        $wrPatient->update(['surveyname' => 'NEW']);
        $this->assertEquals('OLD', $wrPatient->fresh()->surveyname);

        // Cant save
        $wrPatient->surveyname = 'NEW';
        $wrPatient->save();
        $this->assertEquals('OLD', $wrPatient->fresh()->surveyname);
    }

    public function test_if_a_wildlife_recovery_patients_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $wrPatient = WildlifeRecoveryPatient::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $wrPatient->patient->refresh();

        $wrPatient->delete();
        $this->assertDatabaseHas('wildlife_recovery_patients', ['id' => $wrPatient->id]);
    }
}
