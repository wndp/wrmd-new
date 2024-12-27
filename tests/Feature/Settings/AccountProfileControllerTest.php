<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Enums\SettingKey;
use App\Events\TeamUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class AccountProfileControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_account_profile(): void
    {
        $this->get(route('account.profile.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_account_profile(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $this->actingAs($me->user)->get(route('account.profile.edit'))->assertForbidden();
    }

    public function test_it_displays_the_account_profile_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('account.profile.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/AccountProfile')
                    ->hasOptions(['countryOptions', 'subdivisionOptions', 'timezoneOptions']);
            });
    }

    public function test_a_organization_name_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    public function test_a_country_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'))
            ->assertInvalid(['country' => 'The country field is required.']);

        $this->actingAs($me->user)->put(route('account.profile.update'), ['country' => 'xx'])
            ->assertInvalid(['country' => 'The selected country is invalid.']);
    }

    public function test_a_subdivision_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'))
            ->assertInvalid(['subdivision' => 'The subdivision field is required.']);

        $this->actingAs($me->user)->put(route('account.profile.update'), ['country' => 'US', 'subdivision' => 'xx'])
            ->assertInvalid(['subdivision' => 'The selected subdivision is invalid.']);
    }

    public function test_a_city_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'))
            ->assertInvalid(['city' => 'The city field is required.']);
    }

    public function test_an_address_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'))
            ->assertInvalid(['address' => 'The address field is required.']);
    }

    public function test_a_photo_must_be_an_acceptable_type(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'), [
            'photo' => UploadedFile::fake()->image('avatar.tiff'),
        ])
            ->assertInvalid(['photo' => 'The photo field must be a file of type: jpg, jpeg, png.']);
    }

    public function test_the_account_profile_is_updated_in_storage(): void
    {
        Bus::fake();
        Event::fake();
        Storage::fake('s3');

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update'), [
            'name' => 'My Wildlife Rescue',
            'country' => 'US',
            'subdivision' => 'US-CA',
            'city' => 'Monterey',
            'address' => '123 Main st',
            'postal_code' => '918273',
            'federal_permit_number' => 'M-123',
            'subdivision_permit_number' => 'CA-456',
            'photo' => ($file = UploadedFile::fake()->image('avatar.jpg')),
        ])
            ->assertRedirect(route('account.profile.edit'));

        $this->assertDatabaseHas('teams', [
            'id' => $me->team->id,
            'name' => 'My Wildlife Rescue',
            'country' => 'US',
            'subdivision' => 'US-CA',
            'city' => 'Monterey',
            'address' => '123 Main st',
            'postal_code' => '918273',
            'federal_permit_number' => 'M-123',
            'subdivision_permit_number' => 'CA-456',
            'profile_photo_path' => 'profile-photos/'.$file->hashName(),
        ]);

        Storage::disk('s3')->assertExists('profile-photos/'.$file->hashName());

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });
    }

    public function test_a_contact_name_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.contact'))
            ->assertInvalid(['contact_name' => 'The contact name field is required.']);
    }

    public function test_a_phone_number_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.contact'))
            ->assertInvalid(['phone' => 'The phone field is required.']);

        $this->actingAs($me->user)->put(route('account.profile.update.contact'), ['phone' => '123'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    public function test_an_emial_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.contact'))
            ->assertInvalid(['contact_email' => 'The contact email field is required.']);

        $this->actingAs($me->user)->put(route('account.profile.update.contact'), ['contact_email' => 'foo'])
            ->assertInvalid(['contact_email' => 'The contact email field must be a valid email address.']);
    }

    public function test_the_account_contact_is_updated_in_storage(): void
    {
        Bus::fake();
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.contact'), [
            'contact_name' => 'Jim Halpert',
            'phone' => '(808) 555-3432',
            'contact_email' => 'jim@dundermifflin.com',
            'website' => 'www.dundermifflin.com',
        ])
            ->assertRedirect(route('account.profile.edit'));

        $this->assertDatabaseHas('teams', [
            'id' => $me->team->id,
            'contact_name' => 'Jim Halpert',
            'phone' => '(808) 555-3432',
            'contact_email' => 'jim@dundermifflin.com',
            'website' => 'www.dundermifflin.com',
        ]);

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });
    }

    public function test_a_timezone_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.localization'))
            ->assertInvalid(['timezone' => 'The timezone field is required.']);
    }

    public function test_a_language_is_required_to_update_the_account_profile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.localization'))
            ->assertInvalid(['language' => 'The language field is required.']);
    }

    public function test_the_account_localization_is_updated_in_storage(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->put(route('account.profile.update.localization'), [
            'timezone' => 'America/New_York',
            'language' => 'en',
        ])
            ->assertRedirect(route('account.profile.edit'));

        Event::assertDispatched(function (TeamUpdated $event) use ($me) {
            return $event->team->id === $me->team->id;
        });

        $this->assertTeamHasSetting($me->team, SettingKey::TIMEZONE, 'America/New_York');
        $this->assertTeamHasSetting($me->team, SettingKey::LANGUAGE, 'en');
    }
}
