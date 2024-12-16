<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class UserProfileControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessUserProfile(): void
    {
        $this->get(route('profile.edit'))->assertRedirect('login');
    }

    #[Test]
    public function itDisplaysTheUserProfilePage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Profile/Edit');
            });
    }

    #[Test]
    public function aNameIsRequiredToUpdateTheUserProfile(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update'))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    #[Test]
    public function anEmailIsRequiredToUpdateTheUserProfile(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update'))
            ->assertInvalid(['email' => 'The email field is required.']);

        $this->actingAs($me->user)->put(route('profile.update'), ['email' => 'foo'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);
    }

    #[Test]
    public function theUserProfileIsUpdatedInStorage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)
            ->from(route('profile.edit'))
            ->put(route('profile.update'), [
                'name' => 'Pam Beasly',
                'email' => 'pam@dundermifflin.com',
            ])
            ->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $me->user->id,
            'name' => 'Pam Beasly',
            'email' => 'pam@dundermifflin.com',
        ]);
    }
}
