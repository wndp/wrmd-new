<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\SettingKey;
use App\Events\TeamUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class PrivacyControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_privacy_settings(): void
    {
        $this->get(route('privacy.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_privacy_settings(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('privacy.edit'))->assertForbidden();
    }

    public function test_it_displays_the_privacy_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('privacy.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Privacy')
                    ->hasAll('users', 'fullPeopleAccess', 'authorized');
            });
    }

    public function test_the_privacy_settings_are_updated(): void
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $me = $this->createTeamUser();

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        BouncerFacade::allow($me->user)->to(Ability::COMBINE_PEOPLE->value);

        $this->actingAs($me->user)->put(route('privacy.update'), [
            'fullPeopleAccess' => '0',
            'displayPeople' => [$me->user->email],
            'displayRescuer' => [$me->user->email],
            'searchRescuers' => [$me->user->email],
            'createPeople' => [$me->user->email],
            'deletePeople' => [],
            'exportPeople' => [],
            'combinePeople' => [],
        ])
            ->assertRedirect(route('privacy.edit'));

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });

        $this->assertTeamHasSetting($me->team, SettingKey::FULL_PEOPLE_ACCESS, '0');
        $this->assertTrue($me->user->can('display-people'));
        $this->assertTrue($me->user->can('display-rescuer'));
        $this->assertTrue($me->user->can('search-rescuers'));
        $this->assertTrue($me->user->can('create-people'));
        $this->assertFalse($me->user->can('delete-people'));
        $this->assertFalse($me->user->can('export-people'));
        $this->assertFalse($me->user->can('combine-people'));
    }
}
