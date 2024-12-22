<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class UserProfileControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_user_profile(): void
    {
        $this->get(route('profile.edit'))->assertRedirect('login');
    }

    public function test_it_displays_the_user_profile_page(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Profile/Edit');
            });
    }

    public function test_a_name_is_required_to_update_the_user_profile(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update'))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    public function test_an_email_is_required_to_update_the_user_profile(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->put(route('profile.update'))
            ->assertInvalid(['email' => 'The email field is required.']);

        $this->actingAs($me->user)->put(route('profile.update'), ['email' => 'foo'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);
    }

    public function test_the_user_profile_is_updated_in_storage(): void
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
