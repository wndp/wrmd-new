<?php

namespace Tests\Feature\Patients\Concerns;

use App\Domain\Patients\Concerns\RetrievesTreatmentLogs;
use App\Domain\Patients\Exam;
use App\Domain\Patients\Patient;
use App\Domain\Patients\PatientLocation;
use App\Domain\Patients\TreatmentLog;
use App\Domain\Taxonomy\Taxon;
use App\Events\GetTreatmentLogs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class RetrievesTreatmentLogsTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;
    use RetrievesTreatmentLogs;

    protected $me;

    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
        $this->me = $this->createAccountUser();
        $this->setSetting($this->me->account, 'date_format', 'Y-m-d');

        $this->patient = Patient::factory()->create();
    }

    public function test_it_gets_the_treatment_logs(): void
    {
        Auth::loginUsingId($this->me->user->id);
        TreatmentLog::factory()->create(['patient_id' => $this->patient->id]);

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(TreatmentLog::class, $logs->first()->model);
    }

    public function test_it_dispatches_a_get_treatment_logs_event(): void
    {
        Event::fake();

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        Event::assertDispatched(GetTreatmentLogs::class, function ($e) {
            return $e->patientId === $this->patient->id;
        });
    }

    public function test_it_gets_the_treatment_logs_in_ascending_order(): void
    {
        Auth::loginUsingId($this->me->user->id);
        TreatmentLog::factory()->create(['patient_id' => $this->patient->id, 'treated_at' => '2017-02-10']);
        PatientLocation::factory()->create(['patient_id' => $this->patient->id, 'moved_in_at' => '2017-02-11']);
        Exam::factory()->create(['patient_id' => $this->patient->id, 'examined_at' => '2017-02-12']);

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user,
            false
        );

        $this->assertCount(3, $logs);
        $this->assertInstanceOf(TreatmentLog::class, $logs[0]->model);
        $this->assertInstanceOf(PatientLocation::class, $logs[1]->model);
        $this->assertInstanceOf(Exam::class, $logs[2]->model);
    }

    public function test_viewers_can_not_edit_or_delete_a_treatment_log(): void
    {
        Auth::loginUsingId($this->me->user->id);
        $this->me->user->switchRoleTo('viewer');

        TreatmentLog::factory()->create(['patient_id' => $this->patient->id]);

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_edit);
        $this->assertFalse($logs->first()->can_delete);
    }

    public function test_authors_can_edit_thier_treatment_logs_if_authorized(): void
    {
        Auth::loginUsingId($this->me->user->id);
        TreatmentLog::factory()->create(['patient_id' => $this->patient->id, 'user_id' => $this->me->user->id]);

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_edit);

        $this->setSetting($this->me->account, 'logAllowAuthorEdit', true);
        app()->forgetInstance('AccountSettings');

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertTrue($logs->first()->can_edit);
        $this->assertTrue($logs->first()->can_delete);
    }

    public function test_users_can_edit_treatment_logs_if_authorized_if_authorized_by_thier_role(): void
    {
        Auth::loginUsingId($this->me->user->id);
        TreatmentLog::factory()->create(['patient_id' => $this->patient->id, 'user_id' => $this->me->user->id]);

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_edit);

        BouncerFacade::allow($this->me->user)->to('manage-treatment-logs');

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertTrue($logs->first()->can_edit);
    }

    public function test_users_can_delete_treatment_logs_if_authorized_by_thier_role(): void
    {
        Auth::loginUsingId($this->me->user->id);
        TreatmentLog::factory()->create(['patient_id' => $this->patient->id, 'user_id' => $this->me->user->id]);

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_delete);

        BouncerFacade::allow($this->me->user)->to('manage-treatment-logs');

        $logs = $this->getTreatmentLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertTrue($logs->first()->can_delete);
    }
}
