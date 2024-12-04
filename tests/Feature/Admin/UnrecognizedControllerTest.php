<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class UnrecognizedControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unauthenticatedUsersCantAccessUnrecognizedPatients(): void
    {
        $this->get(route('taxa.unrecognized.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessUnrecognizedPatients(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('taxa.unrecognized.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheUnidentifiedSpeciesIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('taxa.unrecognized.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Taxa/Unrecognized')
                    ->has('unrecognizedPatients');
            });
    }
}
