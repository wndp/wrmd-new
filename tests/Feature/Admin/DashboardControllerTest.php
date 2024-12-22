<?php

namespace Tests\Feature\Admin;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class DashboardControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_accounts(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_accounts(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_it_displays_the_accounts_index_page(): void
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
