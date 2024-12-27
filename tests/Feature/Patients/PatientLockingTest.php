<?php

namespace Tests\Feature\Patients;

use App\Domain\Taxonomy\Taxon;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class PatientLockingTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_lock_a_patient_to_me(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id, 'case_year' => date('Y')]);

        $this->actingAs($me->user)
            ->get(route('patients.initial.edit', ['c' => $admission->id, 'y' => $admission->case_year]))
            ->assertOk();

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'locked_to' => $me->user->id,
        ]);
    }

    public function test_viewers_can_not_lock_patients(): void
    {
        $me = $this->createAccountUser([], ['role' => 'Viewer']);
        $admission = $this->createCase(['account_id' => $me->account->id, 'case_year' => date('Y')]);

        $response = $this->actingAs($me->user)
            ->get(route('patients.initial.edit', ['c' => $admission->id, 'y' => $admission->case_year]))
            ->assertOk();

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'locked_to' => null,
        ]);
    }
}
