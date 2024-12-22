<?php

namespace Tests\Feature\SubAccounts;

use App\Enums\Ability;
use App\Enums\SettingKey;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class SubAccountsSettingsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_sub_accounts_settings(): void
    {
        $team = Team::factory()->create();

        $this->get(route('sub_accounts.settings.edit', $team))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_sub_accounts_settings(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.settings.edit', $team))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_sub_account_before_displaying_the_settings_page(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.settings.edit', $team))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_sub_account_settings_edit_page(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->get(route('sub_accounts.settings.edit', $team))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('SubAccounts/Settings')
                    ->has('subAccountSettings')
                    ->where('subAccount.id', $team->id)
            );
    }

    public function test_it_validates_ownership_of_an_sub_account_before_updating_the_settings(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->put(route('sub_accounts.settings.update', $team))
            ->assertOwnershipValidationError();
    }

    public function test_a_sub_account_is_updated_in_storage(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('sub_accounts.settings.update', $team), [
            'sub_account_allow_manage_settings' => true,
            'sub_account_allow_transfer_patients' => false,
        ])
            ->assertRedirect(route('sub_accounts.settings.edit', $team));

        $this->assertTeamHasSetting($team, SettingKey::SUB_ACCOUNT_ALLOW_MANAGE_SETTINGS, true);
        $this->assertTeamHasSetting($team, SettingKey::SUB_ACCOUNT_ALLOW_TRANSFER_PATIENTS, false);
    }
}
