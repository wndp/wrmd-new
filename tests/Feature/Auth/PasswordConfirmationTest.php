<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class PasswordConfirmationTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('password.confirm'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Auth/ConfirmPassword');
            });
    }

    public function test_password_can_be_confirmed(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->post('/user/confirm-password', [
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->post('/user/confirm-password', [
            'password' => 'wrong-password',
        ])
            ->assertInvalid(['password' => 'The provided password was incorrect.']);
    }
}
