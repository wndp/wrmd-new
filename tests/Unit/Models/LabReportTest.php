<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\LabReport;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('lab')]
final class LabReportTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use GetsCareLogs;
    use RefreshDatabase;

    public function test_a_lab_report_belongs_to_a_patient(): void
    {
        $labReport = LabReport::factory()->make();
        $this->assertInstanceOf(Patient::class, $labReport->patient);
    }

    public function test_it_filters_lab_reports_into_the_care_log(): void
    {
        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);
        //$this->setSetting($me->account, 'date_format', 'Y-m-d');

        $patient = Patient::factory()->create();
        LabReport::factory()->create([
            'patient_id' => $patient->id,
            'analysis_date_at' => '2017-04-08',
            // 'test' => 'Fecal',
            // 'data' => ['float' => 'negative'],
            // 'comments' => 'test',
            // 'technician' => 'bob',
        ]);

        $logs = $this->getCareLogs(
            $patient,
            $me->user
        );

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(LabReport::class, $logs[0]->model);
        $this->assertEquals('2017-04-07 17:00:00', $logs[0]->logged_at_date_time);
        //$this->assertEquals('FECAL: Float=negative, test. Technician: bob', $logs[0]->body);
    }

    public function test_a_lab_report_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabReport::factory()->create(),
            'created'
        );
    }

    public function test_if_a_lab_reports_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $lab = LabReport::factory()->create(['patient_id' => $patient->id, 'accession_number' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $lab->patient->refresh();

        // Cant update
        $lab->update(['accession_number' => 'NEW']);
        $this->assertEquals('OLD', $lab->fresh()->accession_number);

        // Cant save
        $lab->accession_number = 'NEW';
        $lab->save();
        $this->assertEquals('OLD', $lab->fresh()->accession_number);
    }

    public function test_if_a_lab_reports_patient_is_locked_then_it_can_not_be_created(): void
    {
        $lab = LabReport::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($lab->exists);
    }

    public function test_if_a_lab_reports_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $lab = LabReport::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $lab->patient->refresh();

        $lab->delete();
        $this->assertDatabaseHas('lab_reports', ['id' => $lab->id, 'deleted_at' => null]);
    }

    public function test_when_a_patient_is_replicated_so_are_the_lab_reports(): void
    {
        $patient = Patient::factory()->create();
        LabReport::factory()->create(['patient_id' => $patient->id]);
        LabReport::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, LabReport::where('patient_id', $patient->id)->get());
        $this->assertCount(2, LabReport::where('patient_id', $newPatient->id)->get());
    }
}
