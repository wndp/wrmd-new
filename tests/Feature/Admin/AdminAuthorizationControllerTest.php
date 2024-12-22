<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability as BouncerAbility;
use Silber\Bouncer\Database\Role as BouncerRole;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AdminAuthorizationControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_authorization(): void
    {
        $this->get(route('admin.authorization'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_authorization(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('admin.authorization'))->assertForbidden();
    }

    public function test_it_displays_the_authorization_index_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('admin.authorization'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Authorization')
                    ->hasAll('roles', 'abilities');
            });
    }

    public function test_it_fails_validation_when_trying_to_update_authorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)
            ->put(route('admin.authorization.update', 'allowed'), [
                Role::ADMIN->value => 'view-reports',
            ])
            ->assertInvalid([Role::ADMIN->value => 'The ADMIN allowed abilities must be an array.']);
    }

    public function test_it_updates_the_allowed_authorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->assertDatabaseMissing('permissions', ['entity_type' => 'role']);

        $this->actingAs($me->user)
            ->put(route('admin.authorization.update', 'allowed'), [
                Role::ADMIN->value => ['view-reports'],
            ])
            ->assertRedirect(route('admin.authorization'));

        $this->assertDatabaseHas('permissions', [
            'forbidden' => 0,
            'entity_type' => 'roles',
            'entity_id' => BouncerRole::where('name', 'admin')->first()->id,
            'ability_id' => BouncerAbility::where('name', 'view-reports')->first()->id,
        ]);
    }

    public function test_it_updates_the_forbidden_authorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->assertDatabaseMissing('permissions', ['entity_type' => 'role']);

        $this->actingAs($me->user)
            ->put(route('admin.authorization.update', 'forbidden'), [
                Role::ADMIN->value => ['view-reports'],
            ])
            ->assertRedirect(route('admin.authorization'));

        $this->assertDatabaseHas('permissions', [
            'forbidden' => 1,
            'entity_type' => 'roles',
            'entity_id' => BouncerRole::where('name', Role::ADMIN->value)->first()->id,
            'ability_id' => BouncerAbility::where('name', 'view-reports')->first()->id,
        ]);
    }
}
