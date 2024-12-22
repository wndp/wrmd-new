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
final class DailyTasksControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_daily_tasks_page(): void
    {
        $this->get(route('daily-tasks.index'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_daily_tasks_page(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('daily-tasks.index'))->assertForbidden();
    }

    public function test_it_displays_the_daily_tasks_index_page(): void
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

    public function test_un_authenticated_users_cant_access_patient_daily_tasks_page(): void
    {
        $this->get(route('patients.daily-tasks.edit'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_patient_daily_tasks_page(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get(route('patients.daily-tasks.edit'))->assertForbidden();
    }

    public function test_it_displays_the_patients_daily_tasks_edit_page(): void
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
