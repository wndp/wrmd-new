<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class DailyTasksControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessDailyTasksPage(): void
    {
        $this->get(route('daily-tasks.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessDailyTasksPage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('daily-tasks.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheDailyTasksIndexPage(): void
    {
        $me = $this->createTeamUser();

        BouncerFacade::allow($me->user)->to(Ability::VIEW_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->get(route('daily-tasks.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('DailyTasks/Index')
                    ->hasAll(['filters', 'taskGroups']);
            });
    }

    #[Test]
    public function unAuthenticatedUsersCantAccessPatientDailyTasksPage(): void
    {
        $this->get(route('patients.daily-tasks.edit'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessPatientDailyTasksPage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('patients.daily-tasks.edit'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysThePatientsDailyTasksEditPage(): void
    {
        $me = $this->createTeamUser();

        $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->get(route('patients.daily-tasks.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Patients/DailyTasks')
                    ->hasAll(['filters', 'tasks']);
            });
    }
}
