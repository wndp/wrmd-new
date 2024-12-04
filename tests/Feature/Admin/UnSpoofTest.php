<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Jobs\UnSpoofTeams;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class UnSpoofTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function usersThatAreCurrentlySpoofingAreNotUnspoofed(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();

        $me->user->joinTeam($team, Role::ADMIN);
        $me->user->switchTeam($team);

        dispatch_sync(new UnSpoofTeams());

        $this->assertTrue($me->user->currentTeam->id === $team->id);
    }

    #[Test]
    public function onlyDevinAndRachelAreUnspoofed(): void
    {
        // $me = $this->createTeamUser(['id' => 1], ['id' => 143]);
        // $you = $this->createTeamUser(['id' => 2]);

        // $me->user->joinTeam($you->team, Role::ADMIN);
        // $me->user->switchTeam($you->team);
        // $me->user->switchTeam($me->team);

        // $you->user->joinTeam($me->team, Role::ADMIN);
        // $you->user->switchTeam($me->team);
        // $you->user->switchTeam($you->team);

        // $this->assertCount(2, $me->user->allTeams());
        // $this->assertCount(2, $you->user->allTeams());

        // dispatch_sync(new UnSpoofTeams());

        // $this->assertTrue($me->user->currentTeam->id === 1);
        // $this->assertCount(1, $me->user->refresh()->allTeams());

        // $this->assertTrue($you->user->currentTeam->id === 2);
        // $this->assertCount(2, $you->user->refresh()->allTeams());
    }
}
