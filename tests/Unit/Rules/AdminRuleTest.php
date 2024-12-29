<?php

namespace Tests\Unit\Rules;

use App\Enums\Role;
use App\Rules\AdminRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AdminRuleTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_update_my_role_to_allowed_value(): void
    {
        // Give that I have a team and I am the only user
        $me = $this->createTeamUser();

        // When I try to change my role to Admin
        $result = (new AdminRule($me->user, $me->team))->passes('role', Role::ADMIN->value);

        // Then I should pass validation
        $this->assertTrue($result);
    }

    public function test_update_my_role_to_disallowed_value(): void
    {
        // Give that I have a team and I am the only user
        $me = $this->createTeamUser();

        // When I try to change my role to anything but Admin
        $result = (new AdminRule($me->user, $me->team))->passes('role', Role::USER->value);

        // Then I should fail validation
        $this->assertFalse($result);
    }

    public function test_update_thier_role_to_allowed_value(): void
    {
        // Give that there is a team with two users
        $me = $this->createTeamUser();
        $you = $this->attachUser($me->team);

        // When I try to change their role to anything
        $result = (new AdminRule($you, $me->team))->passes('role', Role::USER->value);

        // Then I should pass validation
        $this->assertTrue($result);
    }

    public function test_update_thier_role_to_disallowed_value(): void
    {
        // Give that there is a team with two users and I am not an Admin
        $me = $this->createTeamUser(role: Role::USER);
        $you = $this->attachUser($me->team);

        // When I try to change their role to anything but Admin
        $result = (new AdminRule($you, $me->team))->passes('role', Role::USER->value);

        // Then I should pass validation
        $this->assertFalse($result);
    }
}
