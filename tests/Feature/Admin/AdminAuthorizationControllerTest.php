<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability as BouncerAbility;
use Silber\Bouncer\Database\Role as BouncerRole;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AdminAuthorizationControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAuthorization(): void
    {
        $this->get(route('admin.authorization'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessAuthorization(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('admin.authorization'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheAuthorizationIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $response = $this->actingAs($me->user)->get(route('admin.authorization'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Authorization')
                    ->hasAll('roles', 'abilities');
            });
    }

    #[Test]
    public function itFailsValidationWhenTryingToUpdateAuthorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)
            ->put(route('admin.authorization.update', 'allowed'), [
                Role::ADMIN->value => 'view-reports',
            ])
            ->assertInvalid([Role::ADMIN->value => 'The ADMIN allowed abilities must be an array.']);
    }

    #[Test]
    public function itUpdatesTheAllowedAuthorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->assertDatabaseMissing('permissions', ['entity_type' => 'role']);

        $response = $this->actingAs($me->user)
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

    #[Test]
    public function itUpdatesTheForbiddenAuthorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->assertDatabaseMissing('permissions', ['entity_type' => 'role']);

        $response = $this->actingAs($me->user)
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
