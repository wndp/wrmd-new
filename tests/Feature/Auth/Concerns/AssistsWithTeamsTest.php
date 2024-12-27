<?php

namespace Tests\Feature\Auth\Concerns;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AssistsWithTeamsTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_a_user_can_join_a_team(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $team = Team::factory()->create();

        $user = $user->joinTeam($team, Role::ADMIN);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(Role::ADMIN->value, $user->roles->first()->name);
        $this->assertTrue($team->allUsers()->contains($user));
    }

    public function test_a_user_cant_join_an_account_they_are_already_an_account_user_of(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $user->joinTeam($team, Role::ADMIN);
        $user->joinTeam($team, Role::ADMIN);

        $this->assertCount(1, $user->refresh()->teams);
    }

    public function test_it_determines_if_user_belongs_to_any_teams(): void
    {
        $user = User::factory()->create();
        $this->assertFalse($user->belongsToAnyTeams());

        $team = Team::factory()->create();
        $user->joinTeam($team, Role::ADMIN);

        $this->assertTrue($user->fresh()->belongsToAnyTeams());
    }

    public function test_it_gets_the_users_teammates(): void
    {
        $userTeam = $this->createTeamUser();
        $otherUser = User::factory()->create();

        $otherUser->joinTeam($userTeam->team, Role::ADMIN);

        $teammates = $otherUser->teammatesOnTeam($userTeam->team);

        $this->assertInstanceOf(Collection::class, $teammates);
        $this->assertCount(2, $teammates);
        $this->assertTrue($teammates->contains($otherUser));
        $this->assertTrue($teammates->contains($userTeam->user));
    }
}
