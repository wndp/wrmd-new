<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AccountsActionsControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAccountActions(): void
    {
        $team = Team::factory()->create();
        $this->get(route('teams.show.actions', $team))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessAccountActions(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();
        $this->actingAs($me->user)->get(route('teams.show.actions', $team))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheAccountsActionsPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
        $team = Team::factory()->create();
        $response = $this->actingAs($me->user)->get(route('teams.show.actions', $team))
            ->assertOk()
            ->assertInertia(function ($page) use ($team) {
                $page->component('Admin/Teams/Actions')
                    ->where('team.id', $team->id);
            });
    }

    // #[Test]
    // public function unAuthorizedUsersCantSpoofAccounts(): void
    // {
    //     $me = $this->createTeamUser();
    //     $team = Team::factory()->create();
    //     $this->actingAs($me->user)->post(route('teams.spoof', $team))->assertForbidden();
    // }

    // #[Test]
    // public function anAccountCanBeSpoofed(): void
    // {
    //     $me = $this->createTeamUser(['id' => 1]);
    //     BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);
    //     $team = Team::factory()->create();

    //     $this->assertEquals(1, $me->user->current_team_id);

    //     $this->actingAs($me->user)->post(route('teams.spoof', $team))
    //         ->assertRedirect('dashboard');

    //     $this->assertEquals($team->id, $me->user->current_team_id);
    // }
}
