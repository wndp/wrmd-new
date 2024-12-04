<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class DashboardControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAccounts(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessAccounts(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('admin.dashboard'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheAccountsIndexPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_WRMD_ADMIN->value);

        $this->actingAs($me->user)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Admin/Dashboard')
                    ->hasAll([
                        'analyticFiltersForAllYears',
                        'analyticFiltersForThisWeek',
                        'analyticFiltersForToday',
                    ]);
            });
    }
}
