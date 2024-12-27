<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Laravel\Jetstream\Mail\TeamInvitation;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class UsersControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_users(): void
    {
        $this->get(route('users.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_users(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('users.index'))->assertForbidden();
    }

    public function test_it_displays_the_user_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('users.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Users/Index')
                    ->has('users');
            });
    }

    public function test_it_displays_the_page_to_create_a_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('users.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Users/Create')
                    ->hasOption('roles');
            });
    }

    public function test_a_name_is_required_to_store_a_new_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('users.store'))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    public function test_a_valid_email_is_required_to_store_a_new_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('users.store'))
            ->assertInvalid(['email' => 'The email field is required.']);

        $this->actingAs($me->user)->post(route('users.store'), ['email' => 'xxx'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);

        $this->actingAs($me->user)->post(route('users.store'), ['email' => 'not-confirmed@domain.com'])
            ->assertInvalid(['email' => 'The email field confirmation does not match.']);

        User::factory()->create(['email' => 'used@domain.com']);
        $this->actingAs($me->user)->post(route('users.store'), [
            'email' => 'used@domain.com',
            'email_confirmation' => 'used@domain.com',
        ])
            ->assertInvalid(['email' => 'The email has already been taken.']);
    }

    public function test_a_role_is_required_to_store_a_new_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('users.store'))
            ->assertInvalid(['role' => 'The role field is required.']);

        $this->actingAs($me->user)->post(route('users.store'), ['role' => 'foo'])
            ->assertInvalid(['role' => 'The selected role is invalid.']);
    }

    public function test_a_new_user_is_invited_and_saved_to_storage(): void
    {
        Mail::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('users.store'), [
            'email' => 'pam@dundermifflin.com',
            'email_confirmation' => 'pam@dundermifflin.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'name' => 'Pam Beesly',
            'role' => Role::USER->value,
        ])
            ->assertRedirect(route('users.index'));

        Mail::assertSent(TeamInvitation::class);

        $this->assertCount(1, $me->team->fresh()->teamInvitations);

        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $me->team->id,
            'name' => 'Pam Beesly',
            'email' => 'pam@dundermifflin.com',
            'role' => Role::USER->value,
        ]);

        // $this->assertSame(
        //     'admin',
        //     User::firstWhere('email', 'pam@dundermifflin.com')->roleOn($me->team)->name
        // );
    }

    public function test_it_validates_ownership_of_a_user_before_editting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create();

        $this->actingAs($me->user)->get(route('users.edit', $user->id))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_page_to_edit_a_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create(); // ['parent_team_id' => $me->team->id]
        $user->joinTeam($me->team, Role::ADMIN);

        $this->actingAs($me->user)
            ->get(route('users.edit', $user->id))
            ->assertOk()
            ->assertInertia(function ($page) use ($user) {
                $page->hasAll(
                    'user',
                    'abilities',
                    'allowedAbilities',
                    'forbiddenAbilities',
                    'unAllowedAbilities'
                )
                    ->hasOption('roles')
                    ->where('user.id', $user->id);
            });
    }

    public function test_an_email_is_required_to_update_a_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $user = User::factory()->create();
        $user->joinTeam($me->team, Role::USER);

        $this->actingAs($me->user)->put(route('users.update', $user->id))
            ->assertInvalid(['email' => 'The email field is required.']);

        $this->actingAs($me->user)->put(route('users.update', $user->id), [
            'email' => 'foo',
        ])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);

        $this->actingAs($me->user)->put(route('users.update', $user->id), [
            'email' => 'foo',
            'email_confirmation' => 'bar',
        ])
            ->assertInvalid(['email' => 'The email field confirmation does not match.']);

        User::factory()->create(['email' => 'used@domain.com']);
        $this->actingAs($me->user)->put(route('users.update', $user->id), [
            'email' => 'used@domain.com',
            'email_confirmation' => 'used@domain.com',
        ])
            ->assertInvalid(['email' => 'The email has already been taken.']);
    }

    public function test_a_name_is_required_to_update_a_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create();
        $user->joinTeam($me->team, Role::USER);

        $this->actingAs($me->user)->put(route('users.update', $user->id))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    public function test_a_role_is_required_to_update_a_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create();
        $user->joinTeam($me->team, Role::USER);

        $this->actingAs($me->user)->put(route('users.update', $user->id))
            ->assertInvalid(['role' => 'The role field is required.']);

        $this->actingAs($me->user)->put(route('users.update', $user->id), ['role' => 'foo'])
            ->assertInvalid(['role' => 'The selected role is invalid.']);
    }

    public function test_it_validates_ownership_of_a_user_before_updating(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create();

        $this->actingAs($me->user)->put(route('users.update', $user->id), [
            'email' => 'email@domain.com',
            'email_confirmation' => 'email@domain.com',
            'name' => 'Dr Nla',
            'role' => Role::USER->value,
        ])
            ->assertOwnershipValidationError();
    }

    public function test_a_user_is_updated_in_storage(): void
    {
        Bus::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create();
        $user->joinTeam($me->team, Role::USER);

        $this->actingAs($me->user)
            ->from(route('users.edit', $user))
            ->put(route('users.update', $user->id), [
                'email' => 'pam@dundermifflin.com',
                'email_confirmation' => 'pam@dundermifflin.com',
                'name' => 'Pam Beesly',
                'role' => Role::USER->value,
            ])
            ->assertRedirect(route('users.edit', $user));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'pam@dundermifflin.com',
            'name' => 'Pam Beesly',
        ]);
    }

    public function test_it_validates_ownership_of_a_user_before_destroying(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create();

        $this->actingAs($me->user)->delete(route('users.destroy', $user->id))
            ->assertOwnershipValidationError();
    }

    public function test_it_destroys_a_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $user = User::factory()->create(); // ['parent_team_id' => $me->team->id]
        $user->joinTeam($me->team, Role::USER);

        $this->actingAs($me->user)->delete(route('users.destroy', $user->id))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseMissing('veterinarians', ['id' => $user->id]);
    }
}
