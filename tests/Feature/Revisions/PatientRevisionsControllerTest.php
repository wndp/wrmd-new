<?php

namespace Tests\Feature\Revisions;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientRevisionsControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_revisions(): void
    {
        $admission = $this->createCase();

        $this->get(route('patients.revisions.index', $admission->patient_id))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_revisions(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase();

        $this->actingAs($me->user)->get(route('patients.revisions.index', $admission->patient_id))->assertForbidden();
    }

    public function test_it_displays_a_list_of_a_patients_revisions(): void
    {
        activity()->enableLogging();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_REVISIONS->value);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)->get(route('patients.revisions.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Patients/Revisions/Index')
                    ->has('activities');
            });
    }

    public function test_it_displays_a_patients_revision(): void
    {
        activity()->enableLogging();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_REVISIONS->value);

        $admission = $this->createCase($me->team);

        $activity = $admission->patient->activities->last();

        $this->actingAs($me->user)->get(route('patients.revisions.show', [$activity->id]))
            ->assertOk()
            ->assertInertia(function ($page) use ($activity) {
                $page->component('Patients/Revisions/Show')
                    ->hasAll(['activity'])
                    ->where('activity.id', $activity->id);
            });
    }
}
