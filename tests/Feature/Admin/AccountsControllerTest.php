<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Enums\AccountStatus;
use App\Events\TeamUpdated;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AccountsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAccounts(): void
    {
        $this->get(route('teams.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessAccounts(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('teams.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheAccountsIndexPage(): void
    {
        Team::factory()->create();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('teams.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Teams/Index')
                    ->has('teams');
            });
    }

    #[Test]
    public function itDisplaysAnAccount(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('teams.show', $team))
            ->assertOk()
            ->assertInertia(function ($page) use ($team) {
                $page->component('Admin/Teams/Show')
                    ->where('team.id', $team->id)
                    ->hasAll([
                        'team',
                        'analyticFiltersForAllYears',
                        'analyticFiltersForThisYear',
                        'analyticFiltersForLastYear',
                        'analyticFiltersForThisWeek',
                    ]);
            });
    }

    #[Test]
    public function itDisplaysAnAccountEditPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('teams.edit', $team))
            ->assertOk()
            ->assertInertia(function ($page) use ($team) {
                $page->component('Admin/Teams/Edit')
                    ->where('team.id', $team->id);
            });
    }

    #[Test]
    public function itFailsValidationWhenTryingToUpdateAnAccount(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)
            ->put(route('teams.update', $team))
            ->assertInvalid([
                'name' => 'The name field is required.',
                'country' => 'The country field is required.',
                'address' => 'The address field is required.',
                'city' => 'The city field is required.',
                'subdivision' => 'The subdivision field is required.',
                'contact_name' => 'The contact name field is required.',
                'phone' => 'The phone field is required.',
                'contact_email' => 'The contact email field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('teams.update', $team), ['contact_email' => 'foo', 'phone' => '123'])
            ->assertInvalid(['contact_email' => 'The contact email field must be a valid email address.'])
            ->assertInvalid(['phone' => 'The phone field must be a valid number.']);
    }

    #[Test]
    public function itUpdatesAnAccount(): void
    {
        Bus::fake();
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)
            ->put(route('teams.update', $team), [
                'status' => AccountStatus::ACTIVE->value,
                'name' => 'foo wildlife place',
                'country' => 'US',
                'address' => '123 Main st',
                'city' => 'Orlando',
                'subdivision' => 'FL',
                'postal_code' => '12345',
                'contact_name' => 'Pam Beezly',
                'phone' => '(925) 234-5433',
                'contact_email' => 'foo@example.com',
                'website' => 'http://fake.com',
                'federal_permit_number' => 'abc123',
                'subdivision_permit_number' => '789qwe',
            ])
            ->assertRedirect(route('teams.edit', $team));

        $this->assertDatabaseHas('teams', [
            'status' => AccountStatus::ACTIVE->value,
            'name' => 'foo wildlife place',
            'country' => 'US',
            'address' => '123 Main st',
            'city' => 'Orlando',
            'subdivision' => 'FL',
            'postal_code' => '12345',
            'contact_name' => 'Pam Beezly',
            'phone' => '(925) 234-5433',
            'contact_email' => 'foo@example.com',
            'website' => 'http://fake.com',
            'federal_permit_number' => 'abc123',
            'subdivision_permit_number' => '789qwe',
        ]);

        Event::assertDispatched(TeamUpdated::class);
    }
}
