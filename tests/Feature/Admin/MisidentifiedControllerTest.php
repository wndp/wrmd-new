<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class MisidentifiedControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_unauthenticated_users_cant_access_misidentified_patients(): void
    {
        $this->get(route('taxa.misidentified.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_misidentified_patients(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('taxa.misidentified.index'))->assertForbidden();
    }

    public function test_it_displays_the_unidentified_species_index_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('taxa.misidentified.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Taxa/Misidentified')
                    ->has('misidentifiedPatients');
            });
    }
}
