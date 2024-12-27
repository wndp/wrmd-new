<?php

namespace Tests\Feature\Patients\Concerns;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Domain\Users\User;
use Illuminate\Support\Facades\Auth;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;

final class LocksPatientTest extends TestCase
{
    use AssistsWithAuthentication;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_an_unselected_patient_isnt_locked_to_anybody(): void
    {
        $user = User::factory()->create();
        $patient = Patient::factory()->create();

        $this->assertFalse($patient->isAlreadyLocked());
    }

    public function test_a_patient_is_not_locked_to_anybody_except_for_me(): void
    {
        $me = $this->createAccountUser();

        Auth::login($me->user);

        $patient = Patient::factory()->create();
        $patient->attemptToLock();

        $this->assertFalse($patient->isAlreadyLocked());
    }

    public function test_a_patient_can_not_be_locked_to_a_viewer(): void
    {
        $me = $this->createAccountUser([], ['role' => 'viewer']);

        Auth::login($me->user);

        $patient = Patient::factory()->create();
        $patient->attemptToLock();

        $this->assertFalse($patient->locked_to === $me->user->id);
    }

    public function test_a_patient_is_locked_to_anybody_including_you(): void
    {
        $me = $this->createAccountUser();
        $you = $this->attachUser($me->account);

        Auth::login($you);

        $patient = Patient::factory()->create();
        $patient->attemptToLock();

        Auth::login($me->user);
        $this->assertTrue($patient->isAlreadyLocked());
    }
}
