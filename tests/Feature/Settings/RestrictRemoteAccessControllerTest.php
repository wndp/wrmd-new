<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\SettingKey;
use App\Events\TeamUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class RestrictRemoteAccessControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_a_clini_ip_is_required_to_update_restrict_remote_access(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('security.remote-access.update'), [
            'remoteRestricted' => 'true',
        ])
            ->assertInvalid(['clinicIp' => 'The clinic ip field is required when remote restricted is accepted.']);
    }

    public function test_a_clini_ip_must_be_an_ip_address_to_update_restrict_remote_access(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('security.remote-access.update'), [
            'remoteRestricted' => 'true',
            'clinicIp' => 1,
        ])
            ->assertInvalid(['clinicIp' => 'The clinic ip field must be a valid IP address.']);
    }

    public function test_the_restrict_remote_access_settings_are_updated(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('security.remote-access.update'), [
            'remoteRestricted' => '1',
            'clinicIp' => '127.0.0.1',
            'roleRemotePermission' => ['ADMIN'],
            'userRemotePermission' => [$me->user->email],
        ])
            ->assertRedirect(route('security.edit'));

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });

        $this->assertTeamHasSetting($me->team, SettingKey::REMOTE_RESTRICTED, true);
        $this->assertTeamHasSetting($me->team, SettingKey::CLINIC_IP, '127.0.0.1');
        $this->assertTeamHasSetting($me->team, SettingKey::ROLE_REMOTE_PERMISSION, ['ADMIN']);
        $this->assertTeamHasSetting($me->team, SettingKey::USER_REMOTE_PERMISSION, [$me->user->email]);
    }
}
