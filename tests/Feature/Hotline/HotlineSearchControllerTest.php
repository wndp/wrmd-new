<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('hotline')]
final class HotlineSearchControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_hotline_search(): void
    {
        $this->get(route('hotline.search.create'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_hotline_search(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('hotline.search.create'))->assertForbidden();
    }

    public function test_it_can_return_the_view_to_search_hotline_records(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_HOTLINE->value);

        $this->actingAs($me->user)->get(route('hotline.search.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Hotline/Search/Create');
            });
    }

    public function test_it_can_return_a_list_of_the_searched_hotline_records(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.search.search'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Hotline/Search/Index')
                    ->has('incidents');
            });
    }
}
