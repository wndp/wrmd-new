<?php

namespace Tests\Feature\Patients;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientLockingTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    // public function test_lock_a_patient_to_me(): void
    // {
    //     $me = $this->createTeamUser();
    //     $admission = $this->createCase(['team_id' => $me->team->id, 'case_year' => date('Y')]);

    //     $this->actingAs($me->user)
    //         ->get(route('patients.initial.edit', ['c' => $admission->id, 'y' => $admission->case_year]))
    //         ->assertOk();

    //     $this->assertDatabaseHas('patients', [
    //         'id' => $admission->patient_id,
    //         'locked_to' => $me->user->id,
    //     ]);
    // }

    // public function test_viewers_can_not_lock_patients(): void
    // {
    //     $me = $this->createTeamUser([], ['role' => 'Viewer']);
    //     $admission = $this->createCase(['team_id' => $me->team->id, 'case_year' => date('Y')]);

    //     $response = $this->actingAs($me->user)
    //         ->get(route('patients.initial.edit', ['c' => $admission->id, 'y' => $admission->case_year]))
    //         ->assertOk();

    //     $this->assertDatabaseHas('patients', [
    //         'id' => $admission->patient_id,
    //         'locked_to' => null,
    //     ]);
    // }
}
