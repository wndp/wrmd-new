<?php

namespace Tests\Feature\SubAccounts;

use App\Domain\Accounts\Account;
use App\Enums\Ability;
use App\Enums\SettingKey;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\Support\AssistsWithTests;
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

    #[Test]
    public function unAuthenticatedUsersCantAccessSubAccountsSettings(): void
    {
        $team = Team::factory()->create();

        $this->get(route('sub_accounts.settings.edit', $team))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessSubAccountsSettings(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.settings.edit', $team))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfASubAccountBeforeDisplayingTheSettingsPage(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.settings.edit', $team))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itDisplaysTheSubAccountSettingsEditPage(): void
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

    #[Test]
    public function itValidatesOwnershipOfAnSubAccountBeforeUpdatingTheSettings(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->put(route('sub_accounts.settings.update', $team))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aSubAccountIsUpdatedInStorage(): void
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
