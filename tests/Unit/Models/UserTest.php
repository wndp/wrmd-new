<?php

namespace Tests\Unit\Models;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class UserTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_a_user_belongs_to_many_teams(): void
    {
        $user = User::factory()->create();
        $userTeam = $this->createTeamUser(userOverrides: $user);

        $user = $user->fresh();

        $this->assertInstanceOf(Collection::class, $user->teams);
        $this->assertTrue($user->teams->first()->is($userTeam->team));
    }

    public function test_a_user_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            User::factory()->create(),
            'created'
        );
    }

    public function test_it_determines_that_the_user_is_a_member_of_a_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $team->users()->attach($user);

        $result = $user->belongsToTeam($team);

        $this->assertTrue($result);
    }

    public function test_it_gets_the_users_role_for_thier_current_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create([
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $user->joinTeamWithRole($team, Role::ADMIN);

        $this->assertEquals(Role::ADMIN->value, $user->roleOnCurrentTeam()->name);
    }

    public function test_it_gets_the_users_role_for_any_of_thier_teams(): void
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

    public function test_it_gets_the_api_user_for_the_provided_team(): void
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
