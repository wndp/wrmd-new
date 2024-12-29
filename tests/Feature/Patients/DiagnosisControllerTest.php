<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class DiagnosisControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_the_diagnosis(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.diagnosis.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_diagnosis(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.diagnosis.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_diagnosis(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->json('put', route('patients.diagnosis.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_updates_the_diagnosis(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->json('put', route('patients.diagnosis.update', $admission->patient), [
                'diagnosis' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'diagnosis' => 'lorem',
        ]);
    }
}
