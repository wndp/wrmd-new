<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Extension;
use App\Enums\Role;
use App\Models\Team;
use App\Models\TeamExtension;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class AccountExtensionsControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_extensions(): void
    {
        $this->get(route('extensions.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_extensions(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('extensions.index'))->assertForbidden();
    }

    public function test_it_displays_the_extensions_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('extensions.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Extensions')
                    ->hasAll(['standardExtensions', 'proExtensions']);
            });
    }

    public function test_an_extension_is_activated_for_the_authenticated_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->assertDatabaseMissing('team_extensions', [
            'team_id' => $me->team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);

        $this->actingAs($me->user)
            ->from(route('extensions.index'))
            ->post(route('extensions.store', Extension::ATTACHMENTS->value))
            ->assertRedirect(route('extensions.index'));

        $this->assertDatabaseHas('team_extensions', [
            'team_id' => $me->team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);
    }

    public function test_an_extension_is_deactivated_for_the_authenticated_user(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        TeamExtension::factory()->create([
            'team_id' => $me->team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);

        $this->actingAs($me->user)
            ->from(route('extensions.index'))
            ->delete(route('extensions.destroy', Extension::ATTACHMENTS->value))
            ->assertRedirect(route('extensions.index'));

        $this->assertDatabaseMissing('team_extensions', [
            'team_id' => $me->team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);
    }

    public function test_it_validates_ownership_of_a_sub_account_before_activating_an_extensions(): void
    {
        $me = $this->createTeamUser(['is_master_account' => true]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->post(route('extensions.store', [Extension::ATTACHMENTS->value, $team]))
            ->assertOwnershipValidationError();
    }

    public function test_a_specified_accounts_extension_is_activated(): void
    {
        $me = $this->createTeamUser(['is_master_account' => true]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->assertDatabaseMissing('team_extensions', [
            'team_id' => $team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);

        $this->actingAs($me->user)
            ->from(route('sub_accounts.extensions.edit', $team))
            ->post(route('extensions.store', [Extension::ATTACHMENTS->value, $team]))
            ->assertRedirect(route('sub_accounts.extensions.edit', $team));

        $this->assertDatabaseHas('team_extensions', [
            'team_id' => $team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);
    }

    public function test_it_validates_ownership_of_an_sub_account_before_deactivating_an_extensions(): void
    {
        $me = $this->createTeamUser(['is_master_account' => true]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->delete(route('extensions.destroy', [Extension::ATTACHMENTS->value, $team]))
            ->assertOwnershipValidationError();
    }

    public function test_a_specified_accounts_extension_is_deactivated(): void
    {
        $me = $this->createTeamUser(['is_master_account' => true]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        TeamExtension::factory()->create([
            'team_id' => $me->team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);

        $this->actingAs($me->user)
            ->from(route('sub_accounts.extensions.edit', $team))
            ->delete(route('extensions.destroy', [Extension::ATTACHMENTS->value, $team]))
            ->assertRedirect(route('sub_accounts.extensions.edit', $team));

        $this->assertDatabaseMissing('team_extensions', [
            'team_id' => $team->id,
            'extension' => Extension::ATTACHMENTS->value,
        ]);
    }
}
