<?php

namespace Tests\Browser\Admin;

use App\Enums\Ability;
use App\Enums\AccountStatus;
use App\Models\Team;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\DuskTestCase;
use Tests\Traits\BrowserMacros;
use Tests\Traits\CreatesTeamUser;

final class AccountsTest extends DuskTestCase
{
    use BrowserMacros;
    use CreatesTeamUser;
    use DatabaseTruncation;

    #[Test]
    public function listAccounts(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        Team::factory()->create(['id' => 99, 'name' => 'Foo Wildlife Care']);
        Team::factory()->create(['id' => 999, 'name' => 'Bar Critter Center']);

        $this->browse(function ($browser) use ($me) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->clickLinkWhenVisible('Admin')
                ->clickLinkWhenVisible('Accounts')
                ->waitForText('Foo Wildlife Care')
                ->assertSee('Bar Critter Center');
        });
    }

    #[Test]
    public function updateAnAccountProfile(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $account = Team::factory()->create(['name' => 'Foo Wildlife Care', 'country' => 'US']);

        $this->browse(function ($browser) use ($me, $account) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->clickLinkWhenVisible('Admin')
                ->clickLinkWhenVisible('Accounts')
                ->clickLinkWhenVisible('Foo Wildlife Care')
                ->waitForText('Return to Accounts')
                ->assertRouteIs('teams.show', $account)
                ->clickLink('Profile')
                ->waitForText('Account Profile')
                ->select('status', AccountStatus::STALE->value)
                ->type('name', 'My Wildlife Care Center')
                ->type('federal_permit_number', 'abc-123')
                ->type('subdivision_permit_number', '123-abc')
                ->select('country', 'US')
                ->type('address', '123 Main st')
                ->type('city', 'Town')
                ->select('subdivision', 'US-CA')
                ->type('postal_code', '12345')
                ->type('contact_name', 'John Doe')
                ->type('phone_number', '808-899-1234')
                ->type('contact_email', 'john@example.com')
                ->type('website', 'http://wrmd.org')
                ->type('notes', 'foo bar')
                ->press('UPDATE ACCOUNT')
                ->waitForText('Saved.');
        });

        $this->browse(function ($browser) use ($account) {
            $browser->visit(route('teams.edit', $account))
                ->assertSelected('status', AccountStatus::STALE->value)
                ->assertInputValue('name', 'My Wildlife Care Center')
                ->assertInputValue('federal_permit_number', 'abc-123')
                ->assertInputValue('subdivision_permit_number', '123-abc')
                ->assertSelected('country', 'US')
                ->assertInputValue('address', '123 Main st')
                ->assertInputValue('city', 'Town')
                ->assertSelected('subdivision', 'US-CA')
                ->assertInputValue('postal_code', '12345')
                ->assertInputValue('contact_name', 'John Doe')
                ->assertInputValue('phone_number', '(808) 899-1234')
                ->assertInputValue('contact_email', 'john@example.com')
                ->assertInputValue('website', 'http://wrmd.org')
                ->assertInputValue('notes', 'foo bar');
        });

        $this->assertDatabaseHas('teams', [
            'id' => $account->id,
            'status' => AccountStatus::STALE->value,
            'name' => 'My Wildlife Care Center',
            'federal_permit_number' => 'abc-123',
            'subdivision_permit_number' => '123-abc',
            'contact_name' => 'John Doe',
            'country' => 'US',
            'address' => '123 Main st',
            'city' => 'Town',
            'subdivision' => 'US-CA',
            'postal_code' => '12345',
            'phone_number' => '8088991234',
            'contact_email' => 'john@example.com',
            'website' => 'http://wrmd.org',
            'notes' => 'foo bar',
        ]);
    }

    #[Test]
    public function searchAccountsOnAllFields(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $account = Team::factory()->create([
            'id' => 999,
            'status' => AccountStatus::ACTIVE,
            'name' => 'foo',
            'country' => 'US',
            'address' => '13 main st',
            'city' => 'town',
            'subdivision' => 'US-CA',
            'postal_code' => '12345',
            'contact_name' => 'Jim',
            'phone_number' => '555-1234',
            'contact_email' => 'e@b.c',
            'website' => 'http://foo.com',
            'federal_permit_number' => 'M23',
            'subdivision_permit_number' => 'ABC',
        ]);

        $this->browse(function ($browser) use ($me, $account) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->clickLinkWhenVisible('Admin')
                ->clickLinkWhenVisible('Accounts')
                ->waitForText('Show other filters')
                ->press('Show other filters')
                ->select('status', AccountStatus::ACTIVE->value)
                ->type('name', 'foo')
                ->select('country', 'US')
                ->type('address', '13 main st')
                ->type('city', 'town')
                ->select('subdivision', 'US-CA')
                ->type('postal_code', '12345')
                ->type('contact_name', 'Jim')
                ->type('phone_number', '555-1234')
                ->type('contact_email', 'e@b.c')
                ->type('website', 'http://foo.com')
                ->type('federal_permit_number', 'M23')
                ->type('subdivision_permit_number', 'ABC')
                ->press('SEARCH')
                ->pause(1500)
                ->assertRouteIs('teams.show', $account);
        });
    }

    #[Test]
    public function deleteAnAccount(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $account = Team::factory()->create(['id' => 999, 'name' => 'Foo Wildlife Care', 'country' => 'US']);

        $this->browse(function ($browser) use ($account, $me) {
            $browser->visit(route('home'))
                ->type('email', $me->user->email)
                ->type('password', 'password')
                ->press('SIGN IN')
                ->clickLinkWhenVisible('Admin')
                ->clickLinkWhenVisible('Accounts')
                ->clickLinkWhenVisible('Foo Wildlife Care')
                ->waitForText('Return to Accounts')
                ->clickLink('Actions')
                ->waitForLink('Delete this account')
                ->clickLink('Delete this account')
                ->waitForText("Are you ABSOLUTELY sure you want to DELETE the account $account->name?")
                ->type('name', $account->name)
                ->type('password', 'password')
                ->type('password_confirmation', 'password')
                ->press(Str::upper("I Understand the Consequences, Delete $account->name"))
                ->waitForText("$account->name is in the queue to be deleted.");
        });

        $this->assertDatabaseMissing('teams', [
            'id' => $account->id,
        ]);
    }
}
