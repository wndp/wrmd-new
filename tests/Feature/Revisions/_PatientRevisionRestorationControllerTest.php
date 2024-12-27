<?php

namespace Tests\Feature\Revisions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientRevisionRestorationControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_restore_revisions(): void
    {
        activity()->enableLogging();

        $admission = $this->createCase();
        $lastRevision = $admission->patient->activities->last();

        $this->put("internal-api/revisions/restore/{$lastRevision->id}")->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_restore_revisions(): void
    {
        activity()->enableLogging();

        $me = $this->createAccountUser();
        $admission = $this->createCase();
        $lastRevision = $admission->patient->activities->last();

        $this->actingAs($me->user)->put("internal-api/revisions/restore/{$lastRevision->id}")->assertForbidden();
    }

    public function test_it_can_restore_all_old_values(): void
    {
        activity()->enableLogging();

        Patient::$disableGeoLocation = true;
        $me = $this->createAccountUser([], ['id' => WrmdStaff::DEVIN]);
        $admission = $this->createCase([], ['common_name' => 'Right bird', 'keywords' => 'pass']);
        $admission->patient->update(['common_name' => 'Wrong bird', 'keywords' => 'fail']);

        $this->assertCount(2, $admission->patient->activities);
        $this->assertEquals('Wrong bird', $admission->patient->common_name);
        $this->assertEquals('fail', $admission->patient->keywords);

        $lastRevision = $admission->patient->activities->last();

        $this->actingAs($me->user)->put("internal-api/revisions/restore/{$lastRevision->id}")->assertOk();

        $patient = $admission->patient->fresh();

        $this->assertCount(3, $patient->activities);
        $this->assertEquals('Right bird', $patient->common_name);
        $this->assertEquals('pass', $patient->keywords);
    }

    public function test_it_can_restore_a_specified_value(): void
    {
        activity()->enableLogging();

        Patient::$disableGeoLocation = true;
        $me = $this->createAccountUser([], ['id' => WrmdStaff::DEVIN]);
        $admission = $this->createCase([], ['common_name' => 'Right bird', 'keywords' => 'pass']);
        $admission->patient->update(['common_name' => 'Wrong bird', 'keywords' => 'fail']);

        $this->assertCount(2, $admission->patient->activities);
        $this->assertEquals('Wrong bird', $admission->patient->common_name);
        $this->assertEquals('fail', $admission->patient->keywords);

        $lastRevision = $admission->patient->activities->last();

        $this->actingAs($me->user)->put("internal-api/revisions/restore/{$lastRevision->id}/common_name")->assertOk();

        $patient = $admission->patient->fresh();

        $this->assertCount(3, $patient->activities);
        $this->assertEquals('Right bird', $patient->common_name);
        $this->assertEquals('fail', $patient->keywords);
    }
}
