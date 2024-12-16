<?php

namespace Tests\Feature\Notifications;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class NotificationsControllerTest extends TestCase
{
    use CreatesTeamUser;

    #[Test]
    public function unAuthenticatedUsersCantAccessNotifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect('login');
    }

    #[Test]
    public function itDisplaysTheNotificationsIndexPage(): void
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
