<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class MisidentifiedControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unauthenticatedUsersCantAccessMisidentifiedPatients(): void
    {
        $this->get(route('taxa.misidentified.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessMisidentifiedPatients(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('taxa.misidentified.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheUnidentifiedSpeciesIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('taxa.misidentified.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Taxa/Misidentified')
                    ->has('misidentifiedPatients');
            });
    }
}
