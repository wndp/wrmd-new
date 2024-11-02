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
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\Support\AssistsWithTests;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('lab')]
final class LabReportTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;
    use GetsCareLogs;
    use CreatesTeamUser;

    #[Test]
    public function aLabReportBelongsToAPatient(): void
    {
        $labReport = LabReport::factory()->make();
        $this->assertInstanceOf(Patient::class, $labReport->patient);
    }

    #[Test]
    public function itFiltersLabReportsIntoTheCareLog(): void
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

    #[Test]
    public function aLabReportIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            LabReport::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifALabReportsPatientIsLockedThenItCanNotBeUpdated(): void
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

    #[Test]
    public function ifALabReportsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $lab = LabReport::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($lab->exists);
    }

    #[Test]
    public function ifALabReportsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $lab = LabReport::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $lab->patient->refresh();

        $lab->delete();
        $this->assertDatabaseHas('lab_reports', ['id' => $lab->id, 'deleted_at' => null]);
    }

    #[Test]
    public function whenAPatientIsReplicatedSoAreTheLabReports(): void
    {
        $patient = Patient::factory()->create();
        LabReport::factory()->create(['patient_id' => $patient->id]);
        LabReport::factory()->create(['patient_id' => $patient->id]);

        $newPatient = $patient->clone();

        $this->assertCount(2, LabReport::where('patient_id', $patient->id)->get());
        $this->assertCount(2, LabReport::where('patient_id', $newPatient->id)->get());
    }
}
