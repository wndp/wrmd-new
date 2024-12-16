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
final class ScheduledTasksControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessScheduledTasksPage(): void
    {
        $this->get(route('patients.scheduled-tasks.edit'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessScheduledTasksPage(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('patients.scheduled-tasks.edit'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheScheduledTasksEditPage(): void
    {
        $me = $this->createTeamUser();

        $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->get(route('patients.scheduled-tasks.edit'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Patients/ScheduledTasks')
                    ->has('tasks');
            });
    }
}
