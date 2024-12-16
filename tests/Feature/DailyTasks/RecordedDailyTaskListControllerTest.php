<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\Ability;
use App\Models\DailyTask;
use App\Models\Recheck;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class RecordedDailyTaskListControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantRecordDailyTasks(): void
    {
        $recheck = Recheck::factory()->create();

        $this->json('post', "internal-api/daily-tasks/record/recheck/{$recheck->id}")
            ->assertUnauthorized();
    }

    #[Test]
    public function unAuthorizedUsersCantRecordDailyTasks(): void
    {
        $me = $this->createTeamUser();

        $recheck = Recheck::factory()->create();

        $this->actingAs($me->user)
            ->json('post', "internal-api/daily-tasks/record/recheck/{$recheck->id}")
            ->assertForbidden();
    }

    #[Test]
    public function itStoresANewDailyTasksRecordedInStorage(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->json('post', "internal-api/daily-tasks/record/recheck/{$recheck->id}", [
                'occurrence' => 1,
                'occurrence_at' => '2020-07-16',
                'completed_at' => '2020-07-17 11:59:00',
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('daily_tasks', [
            'task_type' => Relation::getMorphAlias(Recheck::class),
            'task_id' => $recheck->id,
            'occurrence' => 1,
            'occurrence_at' => '2020-07-16',
            'completed_at' => '2020-07-17 18:59:00',
        ]);
    }

    #[Test]
    public function itRemovesACompleteDailyTaskRecordFromStorage(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $check = tap(DailyTask::factory()->make(), function ($check) use ($recheck) {
            $check->occurrence = 2;
            $check->occurrence_at = '2020-07-16';
            $check->completed_at = '2020-07-17 11:59:00';
            $recheck->recordedTasks()->save($check);
        });

        $this->assertDatabaseHas('daily_tasks', [
            'task_type' => Relation::getMorphAlias(Recheck::class),
            'task_id' => $recheck->id,
            'occurrence' => 2,
            'occurrence_at' => '2020-07-16',
            'completed_at' => '2020-07-17 11:59:00',
        ]);

        $this->actingAs($me->user)
            ->json('delete', "internal-api/daily-tasks/record/recheck/{$recheck->id}", [
                'occurrence' => 2,
                'occurrence_at' => '2020-07-16',
            ])
            ->assertStatus(200);

        $this->assertDatabaseMissing('daily_tasks', [
            'task_type' => Relation::getMorphAlias(Recheck::class),
            'task_id' => $recheck->id,
            'occurrence' => 2,
            'occurrence_at' => '2020-07-16',
            'completed_at' => '2020-07-17 11:59:00',
        ]);
    }
}
