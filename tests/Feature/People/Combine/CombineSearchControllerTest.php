<?php

namespace Tests\Feature\People\Combine;

use App\Enums\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class CombineSearchControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_combine_people(): void
    {
        $this->get(route('people.combine.search'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_combine_people(): void
    {
        $me = $this->createTeamUser(role: Role::USER);

        $this->actingAs($me->user)->get(route('people.combine.search'))->assertForbidden();
    }

    public function test_it_displays_the_combine_people_search_page(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('people.combine.search'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('People/Combine/Search');
            });
    }
}
