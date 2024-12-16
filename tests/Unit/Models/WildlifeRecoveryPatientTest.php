<?php

namespace Tests\Unit\Models;

use App\Models\Patient;
use App\Models\Team;
use App\Models\WildlifeRecoveryPatient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('oil')]
final class WildlifeRecoveryPatientTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aWildlifeRecoveryPatientBelongsToAPatient(): void
    {
        $this->assertInstanceOf(Patient::class, WildlifeRecoveryPatient::factory()->create([
            'patient_id' => Patient::factory(),
        ])->patient);
    }

    #[Test]
    public function aWildlifeRecoveryPatientBelongsToATeam(): void
    {
        $this->assertInstanceOf(Team::class, WildlifeRecoveryPatient::factory()->create()->team);
    }

    #[Test]
    public function aWildlifeRecoveryPatientCanBeFoundByItsQrCodeHash(): void
    {
        WildlifeRecoveryPatient::factory()->create([
            'qr_code' => 'https://wr.md/abc123',
        ]);

        $wr = WildlifeRecoveryPatient::findByHash('abc123');

        $this->assertInstanceOf(WildlifeRecoveryPatient::class, $wr);
        $this->assertEquals('https://wr.md/abc123', $wr->qr_code);
    }

    #[Test]
    public function aWildlifeRecoveryPatientIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            WildlifeRecoveryPatient::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifAWildlifeRecoveryPatientsPatientIsLockedThenItCanNotBeUpdated(): void
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

    #[Test]
    public function ifAWildlifeRecoveryPatientsPatientIsLockedThenItCanNotBeDeleted(): void
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
