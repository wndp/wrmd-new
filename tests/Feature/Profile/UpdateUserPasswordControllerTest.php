<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class UpdateUserPasswordControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function theCurrentPasswordIsRequiredToUpdateTheUserPassword(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update.password'))
            ->assertInvalid(['current_password' => 'The current password field is required.']);

        $this->actingAs($me->user)->put(route('profile.update.password'), [
            'current_password' => 'wrong-password',
        ])
            ->assertInvalid(['current_password' => 'The provided password does not match your current password.']);
    }

    #[Test]
    public function aConfirmedPasswordIsRequiredToUpdateTheUserPassword(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update.password'))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->put(route('profile.update.password'), ['password' => 'foo'])
            ->assertInvalid(['password' => 'The password field must be at least 8 characters.'])
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);
    }

    #[Test]
    public function theUserPasswordIsUpdatedInStorage(): void
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
