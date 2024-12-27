<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AccountProfilePhotoControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_delete_profile_photo(): void
    {
        $this->delete(route('account.profile-photo.destroy'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_delete_profile_photo(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $this->actingAs($me->user)->delete(route('account.profile-photo.destroy'))->assertForbidden();
    }

    public function test_the_accounts_profile_photo_is_deleted(): void
    {
        Storage::fake('s3');

        $me = $this->createTeamUser([
            'profile_photo_path' => 'path/to/profile_photo.jpg',
        ]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->delete(route('account.profile-photo.destroy'))
            ->assertRedirect(route('account.profile.edit'));

        $this->assertDatabaseHas('teams', [
            'id' => $me->team->id,
            'profile_photo_path' => null,
        ]);
    }
}
