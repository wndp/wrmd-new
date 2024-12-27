<?php

namespace Tests\Feature\Auth\Concerns;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\Database\Role as RoleModel;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AssistWithRolesAndAbilitiesTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_role_can_be_retrieved_for_team(): void
    {
        $userTeam = $this->createTeamUser(role: Role::USER);

        $this->assertInstanceOf(RoleModel::class, $userTeam->user->roleOn($userTeam->team));
        $this->assertEquals(Role::USER->value, $userTeam->user->roleOn($userTeam->team)->name);
    }

    public function test_current_role_can_be_retrieved_for_team(): void
    {
        $userTeam = $this->createTeamUser(role: Role::USER);

        $this->assertInstanceOf(RoleModel::class, $userTeam->user->roleOnCurrentTeam());
        $this->assertEquals(Role::USER->value, $userTeam->user->roleOnCurrentTeam()->name);
    }

    public function test_user_has_a_current_role_name_attribute(): void
    {
        $user = User::factory()->create();
        $account = $this->createTeamUser(userOverrides: $user, role: Role::USER);

        $this->assertEquals(Role::USER->label(), $user->authenticated_users_current_role_name_for_humans);
    }

    public function test_it_gets_a_users_role_name_on_an_given_account(): void
    {
        $user = User::factory()->create();
        $otherTeam = Team::factory()->create();
        $userTeam = $this->createTeamUser(userOverrides: $user, role: Role::USER);
        $user->joinTeam($otherTeam, Role::ADMIN);

        $this->assertEquals(Role::USER->label(), $user->getRoleNameOnTeamForHumans($userTeam->team));
        $this->assertEquals(Role::ADMIN->label(), $user->getRoleNameOnTeamForHumans($otherTeam));
    }

    public function test_it_determines_if_the_user_has_a_viewer_role_on_the_current_account(): void
    {
        $user = User::factory()->create();
        $userTeam = $this->createTeamUser(userOverrides: $user, role: Role::USER);

        $this->assertFalse($user->isAViewer($userTeam->team));

        $user->switchRoleTo(Role::VIEWER);
        $this->assertTrue($user->refresh()->isAViewer($userTeam->team));
    }

    public function test_it_switches_the_users_role(): void
    {
        $user = User::factory()->create();
        $userTeam = $this->createTeamUser(userOverrides: $user, role: Role::USER);

        $this->assertSame(Role::USER->value, $user->roleOn($userTeam->team)->name);

        $user->switchRoleTo(Role::VIEWER);
        $this->assertSame(Role::VIEWER->value, $user->refresh()->roleOn($userTeam->team)->name);
    }

    public function test_it_switches_the_users_role_only_for_the_users_active_account(): void
    {
        $user = User::factory()->create();
        $userTeam1 = $this->createTeamUser(userOverrides: $user, role: Role::USER);
        $userTeam2 = $this->createTeamUser(userOverrides: $user, role: Role::ADMIN);

        $this->assertSame(Role::USER->value, $user->roleOn($userTeam1->team)->name);
        $this->assertSame(Role::ADMIN->value, $user->roleOn($userTeam2->team)->name);

        // Users role on team2 is switched because that is the last account
        // the user interacted with and the Bouncer scope is currently assigned there.
        $user->switchRoleTo(Role::VIEWER)->refresh();

        $this->assertSame(Role::USER->value, $user->roleOn($userTeam1->team)->name);
        $this->assertSame(Role::VIEWER->value, $user->roleOn($userTeam2->team)->name);
    }
}
