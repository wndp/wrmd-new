<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Jobs\DeleteAccount;
use App\Jobs\DeleteTeam;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class DeleteAccountsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAccounts(): void
    {
        $team = Team::factory()->create();
        $this->get(route('teams.delete', $team))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessAccounts(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();
        $this->actingAs($me->user)->get(route('teams.delete', $team))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheDeleteAccountView(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->get(route('teams.delete', $team))
            ->assertOk()
            ->assertInertia(function ($page) use ($team) {
                $page->component('Admin/Teams/Delete')
                    ->where('team.id', $team->id);
            });
    }

    #[Test]
    public function theAccountNameIsRequiredToDeleteAnAccount(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->delete(route('teams.destroy', $team))
            ->assertInvalid(['name' => 'The name field is required.']);

        $this->actingAs($me->user)->delete(route('teams.destroy', $team), ['name' => 'foo'])
            ->assertInvalid(['name' => 'The provided organization name does not match the displayed account organization name.']);
    }

    #[Test]
    public function theAuthenticatedUsersPasswordMustBeConfirmedToDeleteAnAccount(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->delete(route('teams.destroy', $team))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('teams.destroy', $team), [
            'password' => 'foo',
            'password_confirmation' => 'bar',
        ])
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);

        $this->actingAs($me->user)->delete(route('teams.destroy', $team), [
            'password' => 'foo',
            'password_confirmation' => 'foo',
        ])
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    #[Test]
    public function theDeleteAccountJobIsFired(): void
    {
        Queue::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->delete(route('teams.destroy', $team), [
            'name' => $team->name,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirect('admin/teams')
            ->assertSessionHas('notification.text', "$team->name is in the queue to be deleted.");

        Queue::assertPushed(DeleteTeam::class, function ($job) use ($team) {
            return $job->team->id === $team->id;
        });
    }
}