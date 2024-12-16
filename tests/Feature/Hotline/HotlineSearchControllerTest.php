<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function unAuthenticatedUsersCantAccessHotlineSearch(): void
    {
        $this->get(route('hotline.search.create'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessHotlineSearch(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('hotline.search.create'))->assertForbidden();
    }

    #[Test]
    public function itCanReturnTheViewToSearchHotlineRecords(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_HOTLINE->value);

        $this->actingAs($me->user)->get(route('hotline.search.create'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Hotline/Search/Create');
            });
    }

    #[Test]
    public function itCanReturnAListOfTheSearchedHotlineRecords(): void
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
