<?php

namespace Tests\Feature\Patients\Concerns;

use App\Domain\Users\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class LocksPatientTest extends TestCase
{
    // use CreatesTeamUser;

    // public function test_an_unselected_patient_isnt_locked_to_anybody(): void
    // {
    //     $user = User::factory()->create();
    //     $patient = Patient::factory()->create();

    //     $this->assertFalse($patient->isAlreadyLocked());
    // }

    // public function test_a_patient_is_not_locked_to_anybody_except_for_me(): void
    // {
    //     $me = $this->createTeamUser();

    //     Auth::login($me->user);

    //     $patient = Patient::factory()->create();
    //     $patient->attemptToLock();

    //     $this->assertFalse($patient->isAlreadyLocked());
    // }

    // public function test_a_patient_can_not_be_locked_to_a_viewer(): void
    // {
    //     $me = $this->createTeamUser([], ['role' => 'viewer']);

    //     Auth::login($me->user);

    //     $patient = Patient::factory()->create();
    //     $patient->attemptToLock();

    //     $this->assertFalse($patient->locked_to === $me->user->id);
    // }

    // public function test_a_patient_is_locked_to_anybody_including_you(): void
    // {
    //     $me = $this->createTeamUser();
    //     $you = $this->attachUser($me->team);

    //     Auth::login($you);

    //     $patient = Patient::factory()->create();
    //     $patient->attemptToLock();

    //     Auth::login($me->user);
    //     $this->assertTrue($patient->isAlreadyLocked());
    // }
}
