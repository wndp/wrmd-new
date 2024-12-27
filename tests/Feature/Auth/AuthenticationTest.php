<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AuthenticationTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Auth/Login');
            });
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $me = $this->createTeamUser();

        $this->post('/login', [
            'email' => $me->user->email,
            'password' => 'password',
        ])
            ->assertRedirect(config('fortify.home'));

        $this->assertAuthenticated();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $me = $this->createTeamUser();

        $this->post(route('login'), [
            'email' => $me->user->email,
            'password' => 'wrong-password',
        ])
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_users_can_log_out(): void
    {
        $me = $this->createTeamUser();
        Auth::login($me->user);

        $this->assertAuthenticated();

        $this->post(route('logout'))->assertRedirect('/');

        $this->assertGuest();
    }

    // #[Test]
    // public function whenAUserLogsOutThePatientLockIsRemoved(): void
    // {
    //     Taxon::factory()->unidentified()->create();
    //     $me = $this->createTeamUser();
    //     $admission = $this->createCase(
    //         ['account_id' => $me->account->id, 'case_year' => date('Y')],
    //         ['locked_to' => $me->user->id]
    //     );
    //     Auth::login($me->user);

    //     $this->assertDatabaseHas('patients', [
    //         'id' => $admission->patient_id,
    //         'locked_to' => $me->user->id,
    //     ]);

    //     $this->post(route('logout'))->assertRedirect('/');

    //     $this->assertGuest();
    //     $this->assertDatabaseHas('patients', [
    //         'id' => $admission->patient_id,
    //         'locked_to' => null,
    //     ]);
    // }
}
