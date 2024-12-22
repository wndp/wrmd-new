<?php

namespace Tests\Feature\SubAccounts;

use App\Actions\RegisterSubAccount;
use App\Enums\Ability;
use App\Enums\Extension;
use App\Enums\Role;
use App\Enums\SettingKey;
use App\Models\CustomField;
use App\Models\User;
use App\Support\ExtensionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class RegisterSubAccountTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_the_sub_account_is_created(): void
    {
        $me = $this->createTeamUser();

        //BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        RegisterSubAccount::run($me->user, new Request([
            'name' => 'Supper Cool Rehab',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'subdivision' => 'CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim Halpert',
            'phone' => '(925) 555-1234',
            'contact_email' => 'name@email.com',
        ]));

        $this->assertDatabaseHas('teams', [
            'master_account_id' => $me->team->id,
            'name' => 'Supper Cool Rehab',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'subdivision' => 'CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim Halpert',
            'phone' => '(925) 555-1234',
            'contact_email' => 'name@email.com',
        ]);
    }

    public function test_a_sub_accounts_settings_are_cloned_from_the_master_account(): void
    {
        $me = $this->createTeamUser();

        $this->setSetting($me->team, SettingKey::TIMEZONE, 'foo_bar');

        $subAccount = RegisterSubAccount::run($me->user, new Request([
            'name' => 'Supper Cool Rehab',
            'contact_name' => 'Jim Halpert',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'contact_email' => 'name@email.com',
            'clone_settings' => true,
        ]));

        $this->assertTeamHasSetting($subAccount, SettingKey::TIMEZONE, 'foo_bar');
    }

    public function test_a_sub_accounts_custom_fields_are_cloned_from_the_master_account(): void
    {
        $me = $this->createTeamUser();

        CustomField::factory()->create(['team_id' => $me->team->id, 'label' => 'foobar']);

        $subAccount = RegisterSubAccount::run($me->user, new Request([
            'name' => 'Supper Cool Rehab',
            'contact_name' => 'Jim Halpert',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'contact_email' => 'name@email.com',
            'clone_custom_fields' => true,
        ]));

        $this->assertTrue($subAccount->fresh()->customFields->contains('label', 'foobar'));
    }

    public function test_a_sub_accounts_users_are_cloned_from_the_master_account(): void
    {
        $me = $this->createTeamUser();

        $jim = User::factory()->create(['name' => 'Jim'])->joinTeam($me->team, Role::ADMIN);
        $pam = User::factory()->create(['name' => 'Pam'])->joinTeam($me->team, Role::USER);

        $subAccount = RegisterSubAccount::run($me->user, new Request([
            'name' => 'Supper Cool Rehab',
            'contact_name' => 'Jim Halpert',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'contact_email' => 'name@email.com',
            'add_current_user' => true,
            'users' => [$jim->id, $pam->id],
        ]));

        $accountUsers = $subAccount->fresh()->allUsers()->load('roles');

        $this->assertTrue($accountUsers->contains('id', $me->user->id));
        $this->assertEquals(Role::ADMIN->value, $accountUsers->firstWhere('id', $me->user->id)->roles->first()->name);

        $this->assertTrue($accountUsers->contains('id', $jim->id));
        $this->assertEquals(Role::ADMIN->value, $accountUsers->firstWhere('id', $jim->id)->roles->first()->name);

        $this->assertTrue($accountUsers->contains('id', $pam->id));
        $this->assertEquals(Role::USER->value, $accountUsers->firstWhere('id', $pam->id)->roles->first()->name);
    }

    public function test_a_sub_accounts_extensions_are_cloned_from_the_master_account(): void
    {
        $me = $this->createTeamUser();

        ExtensionManager::activate($me->team, Extension::OWCN_MEMBER_ORGANIZATION);

        $subAccount = RegisterSubAccount::run($me->user, new Request([
            'name' => 'Supper Cool Rehab',
            'contact_name' => 'Jim Halpert',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'contact_email' => 'name@email.com',
            'clone_extensions' => true,
        ]));

        $subAccount = $subAccount->fresh();

        $this->assertTrue(ExtensionManager::isActivated(Extension::OWCN_MEMBER_ORGANIZATION, $subAccount));
        $this->assertTrue(ExtensionManager::isActivated(Extension::OIL_SPILL_PROCESSING, $subAccount));
    }
}
