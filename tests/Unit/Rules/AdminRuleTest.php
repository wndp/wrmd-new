<?php

namespace Tests\Unit\Rules;

use App\Enums\Role;
use App\Rules\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AdminRuleTest extends TestCase
{
    use CreatesTeamUser;

    //use AssistsWithAuthentication;
    use RefreshDatabase;

    #[Test]
    public function updateMyRoleToAllowedValue(): void
    {
        // Give that I have a team and I am the only user
        $me = $this->createTeamUser();

        // When I try to change my role to Admin
        $result = (new Admin($me->user))->passes('role', Role::ADMIN->value);

        // Then I should pass validation
        $this->assertTrue($result);
    }

    #[Test]
    public function updateMyRoleToDisallowedValue(): void
    {
        // Give that I have a team and I am the only user
        $me = $this->createTeamUser();

        // When I try to change my role to anything but Admin
        $result = (new Admin($me->user))->passes('role', Role::USER->value);

        // Then I should fail validation
        $this->assertFalse($result);
    }

    #[Test]
    public function updateThierRoleToAllowedValue(): void
    {
        // Give that there is a team with two users
        $me = $this->createTeamUser();
        $you = $this->attachUser($me->team);

        // When I try to change their role to anything
        $result = (new Admin($you))->passes('role', Role::USER->value);

        // Then I should pass validation
        $this->assertTrue($result);
    }

    #[Test]
    public function updateThierRoleToDisallowedValue(): void
    {
        // Give that there is a team with two users and I am not an Admin
        $me = $this->createTeamUser(role: Role::USER);
        $you = $this->attachUser($me->team);

        // When I try to change their role to anything but Admin
        $result = (new Admin($you))->passes('role', Role::USER->value);

        // Then I should pass validation
        $this->assertFalse($result);
    }
}
