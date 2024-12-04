<?php

namespace Tests\Browser\Admin;

use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\WrmdStaff;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability as BouncerAbility;
use Silber\Bouncer\Database\Role as BouncerRole;
use Tests\DuskTestCase;
use Tests\Traits\CreatesTeamUser;

final class AuthorizationTest extends DuskTestCase
{
    use CreatesTeamUser;
    use DatabaseTruncation;

    #[Test]
    public function saveAllowedAbilitiesToAvailableRoles(): void
    {
        $me = $this->createTeamUser(role: Role::WNDP_SUPER_ADMIN);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $admin = BouncerRole::create(['name' => Role::ADMIN->value]);
        $viewer = BouncerRole::create(['name' => Role::VIEWER->value]);
        $settings = BouncerAbility::create(['name' => Ability::VIEW_ACCOUNT_SETTINGS->value]);
        $reports = BouncerAbility::create(['name' => Ability::VIEW_REPORTS->value]);

        $this->browse(function ($browser) use ($me) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->waitForText('Admin')
                ->visit(route('admin.authorization'))
                ->waitForText('Default Allowed Authorizations')
                ->click('#allowed_view_reports_viewer')
                ->click('#allowed_view_account_settings_admin')
                ->press('SAVE AUTHORIZATIONS')
                ->waitForText('Default allowed role abilities saved.')
                ->refresh()
                ->waitForText('Default Allowed Authorizations')
                ->assertChecked('#allowed_view_reports_viewer')
                ->assertChecked('#allowed_view_account_settings_admin');
        });

        $this->assertDatabaseHas('permissions', [
            'ability_id' => $settings->id,
            'entity_id' => $admin->id,
            'entity_type' => 'roles',
            'forbidden' => 0,
        ]);

        $this->assertDatabaseHas('permissions', [
            'ability_id' => $reports->id,
            'entity_id' => $viewer->id,
            'entity_type' => 'roles',
            'forbidden' => 0,
        ]);
    }
}
