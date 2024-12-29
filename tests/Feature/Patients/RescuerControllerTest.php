<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class RescuerControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_the_rescuer_view(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.rescuer.edit', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_the_rescuer_view(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->get(route('patients.rescuer.edit', $patient))->assertForbidden();
    }

    public function test_it_displays_the_rescuer_view(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_RESCUER->value);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)->get(route('patients.rescuer.edit'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Rescuer')
                    ->hasAll(['admission', 'rescuer'])
                    ->where('admission.patient_id', $admission->patient_id)
            );
    }
}
