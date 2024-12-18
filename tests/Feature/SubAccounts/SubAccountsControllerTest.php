<?php

namespace Tests\Feature\SubAccounts;

use App\Enums\Ability;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function unAuthenticatedUsersCantAccessSubAccounts(): void
    {
        $this->get(route('sub_accounts.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessSubAccounts(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('sub_accounts.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheSubAccountIndexPage(): void
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

    #[Test]
    public function itDisplaysThePageToCreateANewSubAccount(): void
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

    #[Test]
    public function validDataIsRequiredToCreateASubAccount(): void
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

    #[Test]
    public function aNewSubAccountIsSavedToStorage(): void
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

    #[Test]
    public function itValidatesOwnershipOfASubAccountBeforeDisplayingThePageToShowIt(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.show', $team))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itValidatesOwnershipOfASubAccountBeforeDisplayingThePageToEditIt(): void
    {
        $me = $this->createTeamUser([
            'is_master_account' => true,
        ]);

        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_SETTINGS->value);

        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('sub_accounts.edit', $team))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itDisplaysThePageToEditAnIncident(): void
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

    #[Test]
    public function itValidatesOwnershipOfAnSubAccountBeforeUpdatingIt(): void
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

    #[Test]
    public function validDataIsRequiredToUpdateASubAccount(): void
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

    #[Test]
    public function aSubAccountIsUpdatedInStorage(): void
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
