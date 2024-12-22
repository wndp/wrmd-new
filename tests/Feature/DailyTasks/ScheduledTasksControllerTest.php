<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
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

    public function test_un_authenticated_users_cant_access_scheduled_tasks_page(): void
    {
        $this->get(route('patients.scheduled-tasks.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_scheduled_tasks_page(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('patients.scheduled-tasks.edit'))->assertForbidden();
    }

    public function test_it_displays_the_scheduled_tasks_edit_page(): void
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
