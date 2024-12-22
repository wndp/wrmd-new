<?php

namespace Tests\Feature\SubAccounts;

use App\Enums\Ability;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class SubAccountsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_sub_accounts(): void
    {
        $this->get(route('sub_accounts.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_sub_accounts(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('sub_accounts.index'))->assertForbidden();
    }

    public function test_it_displays_the_sub_account_index_page(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)
            ->get(route('sub_accounts.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('SubAccounts/Index')
                    ->has('subAccounts');
            });
    }

    public function test_it_displays_the_page_to_create_a_new_sub_account(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->get(route('sub_accounts.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('SubAccounts/Create')
                    ->has('users');
            });
    }

    public function test_valid_data_is_required_to_create_a_sub_account(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $this->actingAs($me->user)->post(route('sub_accounts.store'))
            ->assertInvalid(['name' => 'The name field is required.'])
            ->assertInvalid(['country' => 'The country field is required.'])
            ->assertInvalid(['address' => 'The address field is required.'])
            ->assertInvalid(['city' => 'The city field is required.'])
            ->assertInvalid(['subdivision' => 'The subdivision field is required.'])
            ->assertInvalid(['contact_name' => 'The contact name field is required.'])
            ->assertInvalid(['phone' => 'The phone field is required.'])
            ->assertInvalid(['contact_email' => 'The contact email field is required.']);

        $this->actingAs($me->user)->post(route('sub_accounts.store'), ['contact_email' => 'foo', 'phone' => '123'])
            ->assertInvalid(['contact_email' => 'The contact email field must be a valid email address.'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    public function test_a_new_sub_account_is_saved_to_storage(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $response = $this->actingAs($me->user)->post(route('sub_accounts.store'), [
            'name' => 'Supper Cool Rehab',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'subdivision' => 'CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim Jones',
            'phone' => '(925) 555-1234',
            'contact_email' => 'fame@email.com',
        ]);

        $team = Team::where('name', 'Supper Cool Rehab')->first();
        $response->assertRedirect(route('sub_accounts.show', $team->id));

        $this->assertDatabaseHas('teams', [
            'master_account_id' => $me->team->id,
            'name' => 'Supper Cool Rehab',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'subdivision' => 'CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim Jones',
            'phone' => '(925) 555-1234',
            'contact_email' => 'fame@email.com',
        ]);
    }

    public function test_it_validates_ownership_of_a_sub_account_before_displaying_the_page_to_show_it(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.show', $team))
            ->assertOwnershipValidationError();
    }

    public function test_it_validates_ownership_of_a_sub_account_before_displaying_the_page_to_edit_it(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.edit', $team))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_page_to_edit_an_incident(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->actingAs($me->user)->get(route('sub_accounts.edit', $team))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('SubAccounts/Edit')
                    ->where('subAccount.id', $team->id)
            );
    }

    public function test_it_validates_ownership_of_an_sub_account_before_updating_it(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->put(route('sub_accounts.update', $team), [
            'name' => 'Supper Cool Rehab',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'subdivision' => 'CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim Jones',
            'phone' => '(925) 555-1234',
            'contact_email' => 'fame@email.com',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_valid_data_is_required_to_update_a_sub_account(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->actingAs($me->user)->put(route('sub_accounts.update', $team))
            ->assertInvalid(['name' => 'The name field is required.'])
            ->assertInvalid(['country' => 'The country field is required.'])
            ->assertInvalid(['address' => 'The address field is required.'])
            ->assertInvalid(['city' => 'The city field is required.'])
            ->assertInvalid(['subdivision' => 'The subdivision field is required.'])
            ->assertInvalid(['contact_name' => 'The contact name field is required.'])
            ->assertInvalid(['phone' => 'The phone field is required.'])
            ->assertInvalid(['contact_email' => 'The contact email field is required.']);

        $this->actingAs($me->user)->put(route('sub_accounts.update', $team), ['contact_email' => 'foo', 'phone' => '123'])
            ->assertInvalid(['contact_email' => 'The contact email field must be a valid email address.'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    public function test_a_sub_account_is_updated_in_storage(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create(['master_account_id' => $me->team->id]);

        $this->actingAs($me->user)
            ->from(route('sub_accounts.show', $team))
            ->put(route('sub_accounts.update', $team), [
                'name' => 'Supper Cool Rehab',
                'country' => 'US',
                'address' => '123 Main st.',
                'city' => 'Any town',
                'subdivision' => 'CA',
                'postal_code' => '12345',
                'contact_name' => 'Jim Jones',
                'phone' => '(925) 555-1234',
                'contact_email' => 'fame@email.com',
            ])
            ->assertRedirect(route('sub_accounts.show', $team));

        $this->assertDatabaseHas('teams', [
            'master_account_id' => $me->team->id,
            'name' => 'Supper Cool Rehab',
            'country' => 'US',
            'address' => '123 Main st.',
            'city' => 'Any town',
            'subdivision' => 'CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim Jones',
            'phone' => '(925) 555-1234',
            'contact_email' => 'fame@email.com',
        ]);
    }
}
