<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Enums\AccountStatus;
use App\Jobs\DeleteTeam;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class DeleteAccountsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_accounts(): void
    {
        $team = Team::factory()->create();
        $this->get(route('teams.delete', $team))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_accounts(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();
        $this->actingAs($me->user)->get(route('teams.delete', $team))->assertForbidden();
    }

    public function test_it_displays_the_delete_account_view(): void
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

    public function test_the_account_name_is_required_to_delete_an_account(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->delete(route('teams.destroy', $team))
            ->assertInvalid(['name' => 'The name field is required.']);

        $this->actingAs($me->user)->delete(route('teams.destroy', $team), ['name' => 'foo'])
            ->assertInvalid(['name' => 'The provided organization name does not match the displayed account organization name.']);
    }

    public function test_the_account_status_must_be_suspended_to_delete_an_account(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();

        $this->actingAs($me->user)->delete(route('teams.destroy', $team))
            ->assertInvalid(['status' => 'The account status must be Suspended before it can be deleted.']);
    }

    public function test_the_authenticated_users_password_must_be_confirmed_to_delete_an_account(): void
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

    public function test_the_delete_account_job_is_fired(): void
    {
        Queue::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create([
            'status' => AccountStatus::SUSPENDED,
        ]);

        $this->actingAs($me->user)->delete(route('teams.destroy', $team), [
            'name' => $team->name,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirect('admin/dashboard')
            ->assertSessionHas('notification.text', "$team->name is in the queue to be deleted.");

        Queue::assertPushed(DeleteTeam::class, function ($job) use ($team) {
            return $job->team->id === $team->id;
        });
    }
}
