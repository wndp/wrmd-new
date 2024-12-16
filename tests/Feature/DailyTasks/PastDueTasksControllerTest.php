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
final class PastDueTasksControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessPastDueDailyTasksPage(): void
    {
        $this->get(route('patients.past-due-tasks.edit'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessPastDueDailyTasksPage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('patients.past-due-tasks.edit'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysThePatientsPastDueDailyTasksEditPage(): void
    {
        $me = $this->createTeamUser();

        $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->get(route('patients.past-due-tasks.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Patients/PastDueTasks')
                    ->has('pastDueTasks');
            });
    }
}
