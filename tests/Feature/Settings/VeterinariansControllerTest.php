<?php

namespace Tests\Feature\Settings;

use App\Enums\Ability;
use App\Enums\Role;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class VeterinariansControllerTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_veterinarians(): void
    {
        $this->get(route('veterinarians.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_veterinarians(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('veterinarians.index'))->assertForbidden();
    }

    public function test_it_displays_the_veterinarian_settings_page(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('veterinarians.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Settings/Veterinarians/Index')
                    ->has('veterinarians');
            });
    }

    public function test_it_displays_the_page_to_create_a_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('veterinarians.create'))
            ->assertOk()
            ->assertInertia(function ($page) use ($me) {
                $page->component('Settings/Veterinarians/Create')
                    ->has('users')
                    ->where('users.0.value', $me->user->id);
            });
    }

    public function test_a_name_is_required_to_store_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('veterinarians.store'))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    public function test_a_license_is_required_to_store_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('veterinarians.store'))
            ->assertInvalid(['license' => 'The license field is required.']);
    }

    public function test_a_valid_subdivision_must_be_used_to_save_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('veterinarians.store'), ['subdivision' => 'xx'])
            ->assertInvalid(['subdivision' => 'The selected subdivision is invalid.']);
    }

    public function test_a_valid_phone_number_must_be_used_to_save_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('veterinarians.store'), ['phone' => '123'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    public function test_a_valid_email_must_be_used_to_save_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('veterinarians.store'), ['email' => 'foo'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);
    }

    public function test_a_new_veterinarian_is_saved_to_storage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('veterinarians.store'), [
            'name' => 'Dr NLA',
            'license' => 'abc 123',
            'user_id' => $me->user->id,
            'business_name' => 'Fake vet clinic',
            'address' => '123 main st',
            'city' => 'Any town',
            'subdivision' => 'US-CA',
            'postal_code' => '12345',
            'phone' => '(925) 345-1234',
            'email' => 'fake@email.com',
        ])
            ->assertRedirect(route('veterinarians.index'));

        $this->assertDatabaseHas('veterinarians', [
            'team_id' => $me->team->id,
            'user_id' => $me->user->id,
            'name' => 'Dr NLA',
            'license' => 'abc 123',
            'business_name' => 'Fake vet clinic',
            'address' => '123 main st',
            'city' => 'Any town',
            'subdivision' => 'US-CA',
            'postal_code' => '12345',
            'phone' => '(925) 345-1234',
            'email' => 'fake@email.com',
        ]);
    }

    public function test_it_validates_ownership_of_a_veterinarian_before_editting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $veterinarian = Veterinarian::factory()->create();

        $this->actingAs($me->user)->get(route('veterinarians.edit', $veterinarian->id))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_page_to_edit_a_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->get(route('veterinarians.edit', $veterinarian->id))
            ->assertOk()
            ->assertInertia(function ($page) use ($me, $veterinarian) {
                $page->hasAll('users', 'veterinarian')
                    ->where('users.0.value', $me->user->id)
                    ->where('veterinarian.id', $veterinarian->id);
            });
    }

    public function test_a_name_is_required_to_update_a_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    public function test_a_license_is_required_to_update_a_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id))
            ->assertInvalid(['license' => 'The license field is required.']);
    }

    public function test_a_valid_subdivision_must_be_used_to_update_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id), ['subdivision' => 'xx'])
            ->assertInvalid(['subdivision' => 'The selected subdivision is invalid.']);
    }

    public function test_a_valid_phone_number_must_be_used_to_update_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id), ['phone' => '123'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    public function test_a_valid_email_must_be_used_to_update_a_new_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id), ['email' => 'foo'])
            ->assertInvalid(['email' => 'The email field must be a valid email address.']);
    }

    public function test_it_validates_ownership_of_a_veterinarian_before_updating(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $veterinarian = Veterinarian::factory()->create();

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id), [
            'name' => 'Dr NLA',
            'license' => 'abc 123',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_a_veterinarian_is_updated_in_storage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('veterinarians.update', $veterinarian->id), [
            'name' => 'Dr NLA',
            'license' => 'abc 123',
            'user_id' => $me->user->id,
            'business_name' => 'Fake vet clinic',
            'address' => '123 main st',
            'city' => 'Any town',
            'subdivision' => 'US-CA',
            'postal_code' => '12345',
            'phone' => '(925) 345-1234',
            'email' => 'fake@email.com',
        ])
            ->assertRedirect(route('veterinarians.index'));

        $this->assertDatabaseHas('veterinarians', [
            'id' => $veterinarian->id,
            'team_id' => $me->team->id,
            'user_id' => $me->user->id,
            'name' => 'Dr NLA',
            'license' => 'abc 123',
            'business_name' => 'Fake vet clinic',
            'address' => '123 main st',
            'city' => 'Any town',
            'subdivision' => 'US-CA',
            'postal_code' => '12345',
            'phone' => '(925) 345-1234',
            'email' => 'fake@email.com',
        ]);
    }

    public function test_it_validates_ownership_of_a_veterinarian_before_destroying(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $veterinarian = Veterinarian::factory()->create();

        $this->actingAs($me->user)->delete(route('veterinarians.destroy', $veterinarian->id))
            ->assertOwnershipValidationError();
    }

    public function test_it_destroys_a_veterinarian(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);
        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->delete(route('veterinarians.destroy', $veterinarian->id))
            ->assertRedirect(route('veterinarians.index'));

        $this->assertDatabaseMissing('veterinarians', ['id' => $veterinarian->id]);
    }
}
