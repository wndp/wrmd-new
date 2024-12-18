<?php

namespace Tests\Unit\Models;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class UserTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aUserIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            User::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function itDeterminesThatTheUserIsAMemberOfATeam(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $team->users()->attach($user);

        $result = $user->belongsToTeam($team);

        $this->assertTrue($result);
    }

    #[Test]
    public function itGetsTheUsersRoleForThierCurrentTeam(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true
        ]);

        $user->joinTeamWithRole($team, Role::ADMIN);

        $this->assertEquals(Role::ADMIN->value, $user->roleOnCurrentTeam()->name);
    }

    #[Test]
    public function itGetsTheUsersRoleForAnyOfThierTeams(): void
    {
        $user = User::factory()->create();
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        //$this->scopeBouncerTo($team1->id);
        $user->joinTeamWithRole($team1, Role::VIEWER);

        //$this->scopeBouncerTo($team2->id);
        $user->joinTeamWithRole($team2, Role::USER);

        $this->assertEquals(Role::VIEWER->value, $user->roleOn($team1)->name);
        $this->assertEquals(Role::USER->value, $user->roleOn($team2)->name);
    }

    #[Test]
    public function itGetsTheApiUserForTheProvidedTeam(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $apiUser = $user->apiUserFor($team);

        $this->assertSame("api-user-{$team->id}@wrmd.org", $apiUser->email);
        $this->assertSame("API User For {$team->name}", $apiUser->name);
        $this->assertTrue($apiUser->is_api_user);
        $this->assertTrue($apiUser->belongsToTeam($team));
    }
}
