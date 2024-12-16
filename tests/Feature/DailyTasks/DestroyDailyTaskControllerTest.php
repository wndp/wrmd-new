<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\Ability;
use App\Models\Incident;
use App\Models\Recheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function unAuthenticatedUsersCantDestroyDailyTask(): void
    {
        $recheck = Recheck::factory()->create();

        $this->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantDestroyDailyTask(): void
    {
        $me = $this->createTeamUser();

        $recheck = Recheck::factory()->create();

        $this->actingAs($me->user)
            ->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))
            ->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfADailyTaskBeforeDestroy(): void
    {
        $me = $this->createTeamUser();

        $recheck = Recheck::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->delete(route('daily-tasks.delete', ['recheck', $recheck->id]))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function onlySchedulablesCanBeDestroyed(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $incident = Incident::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->delete(route('daily-tasks.delete', ['hotline', $incident->id]))
            ->assertStatus(404);
    }

    #[Test]
    public function itDestroysADailyTasksInStorage(): void
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
