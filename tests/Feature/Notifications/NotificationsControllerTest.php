<?php

namespace Tests\Feature\Notifications;

use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class NotificationsControllerTest extends TestCase
{
    use CreatesTeamUser;

    public function test_un_authenticated_users_cant_access_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect('login');
    }

    public function test_it_displays_the_notifications_index_page(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Notifications')
                    ->has('notifications');
            });
    }
}
