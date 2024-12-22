<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class CombineSearchControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessCombinePeople(): void
    {
        $this->get(route('people.combine.search'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessCombinePeople(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('people.combine.search'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheCombinePeopleSearchPage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('people.combine.search'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('People/Combine/Search');
            });
    }
}
