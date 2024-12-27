<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class UsersAuthorizationsControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_it_validates_ownership_of_a_user_before_updating_the_authorizations(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $user = User::factory()->create();

        $this->actingAs($me->user)->put(route('users.authorizations.update', $user->id), [
            'email' => 'email@domain.com',
            'name' => 'Dr Nla',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_a_users_authorizations_are_updated_in_storage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $user = User::factory()->create(); // ['parent_team_id' => $me->team->id]
        $user->joinTeam($me->team, Role::USER);

        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)->put(route('users.authorizations.update', $user->id), [
            'userAbilities' => [
                'display-daily-tasks' => 'allowed',
                'manage-daily-tasks' => 'allowed',
                'create-patients' => 'forbidden',
                'update-cage-card' => 'forbidden',
            ],
        ])
            ->assertRedirect(route('users.edit', $user));

        $this->assertTrue($user->can('display-daily-tasks'));
        $this->assertTrue($user->can('manage-daily-tasks'));
        $this->assertFalse($user->can('create-patients'));
        $this->assertFalse($user->can('update-cage-card'));
    }
}
