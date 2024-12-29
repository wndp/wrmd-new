<?php

namespace Tests\Feature\Patients\Concerns;

use App\Concerns\GetsCareLogs;
use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\SettingKey;
use App\Events\getCareLogs;
use App\Models\CareLog;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\PatientLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class GetsCareLogsTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use GetsCareLogs;

    protected $me;

    protected $patient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->me = $this->createTeamUser();
        $this->patient = Patient::factory()->create();
    }

    public function test_it_gets_the_treatment_logs(): void
    {
        Auth::loginUsingId($this->me->user->id);
        CareLog::factory()->create(['patient_id' => $this->patient->id]);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(CareLog::class, $logs->first()->model);
    }

    public function test_it_gets_the_treatment_logs_in_ascending_order(): void
    {
        Auth::loginUsingId($this->me->user->id);
        CareLog::factory()->create(['patient_id' => $this->patient->id, 'date_care_at' => '2017-02-10']);
        PatientLocation::factory()->create(['patient_id' => $this->patient->id, 'moved_in_at' => '2017-02-11']);
        Exam::factory()->create(['patient_id' => $this->patient->id, 'date_examined_at' => '2017-02-12']);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user,
            false
        );

        $this->assertCount(3, $logs);
        $this->assertInstanceOf(CareLog::class, $logs[0]->model);
        $this->assertInstanceOf(PatientLocation::class, $logs[1]->model);
        $this->assertInstanceOf(Exam::class, $logs[2]->model);
    }

    public function test_viewers_can_not_edit_or_delete_a_treatment_log(): void
    {
        Auth::loginUsingId($this->me->user->id);
        $this->me->user->switchRoleTo(Role::VIEWER);

        CareLog::factory()->create(['patient_id' => $this->patient->id]);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_edit);
        $this->assertFalse($logs->first()->can_delete);
    }

    public function test_authors_can_edit_thier_treatment_logs_if_authorized(): void
    {
        Auth::loginUsingId($this->me->user->id);
        CareLog::factory()->create(['patient_id' => $this->patient->id, 'user_id' => $this->me->user->id]);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_edit);

        $this->setSetting($this->me->team, SettingKey::LOG_ALLOW_AUTHOR_EDIT, true);
        app()->forgetInstance('AccountSettings');

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertTrue($logs->first()->can_edit);
        $this->assertTrue($logs->first()->can_delete);
    }

    public function test_users_can_edit_treatment_logs_if_authorized_if_authorized_by_thier_role(): void
    {
        Auth::loginUsingId($this->me->user->id);
        CareLog::factory()->create(['patient_id' => $this->patient->id, 'user_id' => $this->me->user->id]);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_edit);

        BouncerFacade::allow($this->me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertTrue($logs->first()->can_edit);
    }

    public function test_users_can_delete_treatment_logs_if_authorized_by_thier_role(): void
    {
        Auth::loginUsingId($this->me->user->id);
        CareLog::factory()->create(['patient_id' => $this->patient->id, 'user_id' => $this->me->user->id]);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertFalse($logs->first()->can_delete);

        BouncerFacade::allow($this->me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $logs = $this->getCareLogs(
            $this->patient,
            $this->me->user
        );

        $this->assertTrue($logs->first()->can_delete);
    }
}
