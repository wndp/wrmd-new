<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\Patient;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class PrescriptionTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use GetsCareLogs;
    use RefreshDatabase;

    public function test_a_prescription_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Prescription::factory()->create(),
            'created'
        );
    }

    public function test_it_filters_prescriptions_into_the_care_log(): void
    {
        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);

        $patient = Patient::factory()->create();

        Prescription::factory()->create([
            'patient_id' => $patient->id,
            'rx_started_at' => '2017-04-09',
            'rx_ended_at' => '2017-04-10',
        ]);

        $logs = $this->getCareLogs($patient, $me->user);

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(Prescription::class, $logs[0]->model);
        $this->assertEquals('2017-04-08 17:00:00', $logs[0]->logged_at_date_time->toDateTimeString());
    }

    public function test_if_a_prescriptions_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $prescription = Prescription::factory()->create(['patient_id' => $patient->id, 'drug' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $prescription->patient->refresh();

        // Cant update
        $prescription->update(['drug' => 'NEW']);
        $this->assertEquals('OLD', $prescription->fresh()->drug);

        // Cant save
        $prescription->drug = 'NEW';
        $prescription->save();
        $this->assertEquals('OLD', $prescription->fresh()->drug);
    }

    public function test_if_a_prescriptions_patient_is_locked_then_it_can_not_be_created(): void
    {
        $prescription = Prescription::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($prescription->exists);
    }

    public function test_if_a_prescriptions_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $prescription = Prescription::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $prescription->patient->refresh();

        $prescription->delete();
        $this->assertDatabaseHas('prescriptions', ['id' => $prescription->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_are_the_prescriptions(): void
    {
        $patient = Patient::factory()->create();

        Prescription::factory()->create(['patient_id' => $patient->id]);
        Prescription::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, Prescription::where('patient_id', $patient->id)->get());
        $this->assertCount(2, Prescription::where('patient_id', $newPatient->id)->get());
    }
}
