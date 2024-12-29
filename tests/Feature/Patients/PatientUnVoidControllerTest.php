<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientUnVoidControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_un_void_a_patient(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.unvoid.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_un_void_a_patient(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->voided()->create();
        $this->actingAs($me->user)->put(route('patients.unvoid.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_un_voiding(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->voided()->create();
        BouncerFacade::allow($me->user)->to(Ability::UN_VOID_PATIENT->value);

        $this->actingAs($me->user)
            ->json('put', route('patients.unvoid.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_un_voids_a_patient(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['voided_at' => Carbon::now()]);
        BouncerFacade::allow($me->user)->to(Ability::UN_VOID_PATIENT->value);

        $voidedPatient = $admission->patient()->withVoided()->first();

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->json('put', route('patients.unvoid.update', $voidedPatient))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'voided_at' => null,
        ]);
    }
}
