<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\SettingKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class SecurityControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_security_settings(): void
    {
        $this->get(route('security.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_security_settings(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('security.edit'))->assertForbidden();
    }

    public function test_it_displays_the_security_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('security.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Security')
                    ->hasAll([
                        'ipAddress',
                        'users',
                        'remoteAccess',
                        'security',
                    ]);
            });
    }

    public function test_the_security_settings_are_updated(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('security.update'), [
            'requireTwoFactor' => '1',
        ])
            ->assertRedirect(route('security.edit'));

        $this->assertTeamHasSetting($me->team, SettingKey::REQUIRE_TWO_FACTOR, true);
    }
}
