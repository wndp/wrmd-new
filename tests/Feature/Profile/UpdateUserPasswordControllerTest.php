<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class UpdateUserPasswordControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_the_current_password_is_required_to_update_the_user_password(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update.password'))
            ->assertInvalid(['current_password' => 'The current password field is required.']);

        $this->actingAs($me->user)->put(route('profile.update.password'), [
            'current_password' => 'wrong-password',
        ])
            ->assertInvalid(['current_password' => 'The provided password does not match your current password.']);
    }

    public function test_a_confirmed_password_is_required_to_update_the_user_password(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update.password'))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->put(route('profile.update.password'), ['password' => 'foo'])
            ->assertInvalid(['password' => 'The password field must be at least 8 characters.'])
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);
    }

    public function test_the_user_password_is_updated_in_storage(): void
    {
        $me = $this->createTeamUser();
        $newPassword = Str::random(8);

        $this->actingAs($me->user)
            ->from(route('profile.edit'))
            ->put(route('profile.update.password'), [
                'current_password' => 'password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check($newPassword, $me->user->fresh()->password));
    }
}
