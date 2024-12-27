<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\SettingKey;
use App\Events\TeamUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class GeneralSettingsControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_general_settings(): void
    {
        $this->get(route('general-settings.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_general_settings(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('general-settings.edit'))->assertForbidden();
    }

    public function test_it_displays_the_general_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('general-settings.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/GeneralSettings')
                    ->hasAll('generalSettings', 'selectableFields', 'listedFields')
                    ->hasOption('roles');
            });
    }

    public function test_the_generic_settings_are_updated(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('general-settings.update'), [
            'showLookupRescuer' => '1',
            'showGeolocationFields' => '0',
            'listFields' => ['foo', 'bar'],
        ])
            ->assertRedirect(route('general-settings.edit'));

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });

        $this->assertTeamHasSetting($me->team, SettingKey::SHOW_LOOKUP_RESCUER, true);
        $this->assertTeamHasSetting($me->team, SettingKey::SHOW_GEOLOCATION_FIELDS, false);
        $this->assertTeamHasSetting($me->team, SettingKey::LIST_FIELDS, ['foo', 'bar']);
    }

    public function test_the_care_log_settings_are_updated(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('general-settings.update.care-log'), [
            'logOrder' => 'asc',
            'logAllowAuthorEdit' => '1',
            'logAllowEdit' => '0',
            'logAllowDelete' => '1',
            'logShares' => '0',
        ])
            ->assertRedirect(route('general-settings.edit'));

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });

        $this->assertTeamHasSetting($me->team, SettingKey::LOG_ORDER, 'asc');
        $this->assertTeamHasSetting($me->team, SettingKey::LOG_ALLOW_AUTHOR_EDIT, true);
        $this->assertTeamHasSetting($me->team, SettingKey::LOG_ALLOW_EDIT, false);
        $this->assertTeamHasSetting($me->team, SettingKey::LOG_ALLOW_DELETE, false);
        $this->assertTeamHasSetting($me->team, SettingKey::LOG_SHARES, false);
    }

    // #[Test]
    // public function theLocationsSettingsAreUpdated(): void
    // {
    //     Event::fake();

    //     $me = $this->createTeamUser();
    //     BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

    //     $this->actingAs($me->user)->put(route('general-settings.update.locations'), [
    //         'areas' => 'treatment room, icu,holding room',
    //         'enclosures' => 'wall cage,incubator, aviary',
    //     ])
    //         ->assertRedirect(route('general-settings.edit'));

    //     // Event::assertDispatched(function (TeamUpdated $event) use ($me) {
    //     //     return $event->team->id === $me->team->id;
    //     // });

    //     $this->assertTeamHasSetting($me->team, 'areas', ['treatment room', 'icu', 'holding room']);
    //     $this->assertTeamHasSetting($me->team, 'enclosures', ['wall cage', 'incubator', 'aviary']);
    // }
}
