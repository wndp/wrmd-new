<?php

namespace Tests\Feature\Maintenance;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class UnrecognizedPatientsControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_unrecognized_patients(): void
    {
        $this->get(route('maintenance.unrecognized-patients'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_unrecognized_patients(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('maintenance.unrecognized-patients'))->assertForbidden();
    }

    public function test_it_displays_the_unrecognized_patients_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $unrecognizedAdmission = $this->createCase($me->team, patientOverrides: ['taxon_id' => null]);

        $this->actingAs($me->user)->get(route('maintenance.unrecognized-patients'))
            ->assertOk()
            ->assertInertia(function ($page) use ($unrecognizedAdmission) {
                $page->component('Maintenance/UnrecognizedPatients')
                    ->where('admissions.data.0.patient_id', $unrecognizedAdmission->patient_id);
            });
    }
}
