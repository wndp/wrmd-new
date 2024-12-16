<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class OtherBrowserSessionsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function testOtherBrowserSessionsCanBeLoggedOut(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)
            ->from(route('profile.edit'))
            ->delete(route('profile.other-browser-sessions.destroy'), [
                'password' => 'password',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasNoErrors();
    }
}
