<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\Ability;
use App\Models\Incident;
use App\Models\Recheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class DestroyDailyTaskControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_destroy_daily_task(): void
    {
        $recheck = Recheck::factory()->create();

        $this->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_destroy_daily_task(): void
    {
        $me = $this->createTeamUser();

        $recheck = Recheck::factory()->create();

        $this->actingAs($me->user)
            ->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))
            ->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_daily_task_before_destroy(): void
    {
        $me = $this->createTeamUser();

        $recheck = Recheck::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))
            ->assertOwnershipValidationError();
    }

    public function test_only_schedulables_can_be_destroyed(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $incident = Incident::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->delete(route('daily-tasks.delete', ['hotline', $incident->id]))
            ->assertStatus(404);
    }

    public function test_it_destroys_a_daily_tasks_in_storage(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from('foo')
            ->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))
            ->assertRedirect('foo');

        $this->assertSoftDeleted('rechecks', [
            'id' => $recheck->id,
        ]);
    }
}
