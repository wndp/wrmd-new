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

final class ClassificationsControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_classifications_settings(): void
    {
        $this->get(route('classification-tagging.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_classifications_settings(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('classification-tagging.edit'))->assertForbidden();
    }

    public function test_it_displays_the_classifications_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('classification-tagging.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Classifications')
                    ->hasAll('showTags');
            });
    }

    public function test_the_classifications_settings_are_updated(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('classification-tagging.update'), [
            'showTags' => '1',
        ])
            ->assertRedirect(route('classification-tagging.edit'));

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });

        $this->assertTeamHasSetting($me->team, SettingKey::SHOW_TAGS, true);
    }
}
