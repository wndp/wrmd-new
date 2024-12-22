<?php

namespace Tests\Feature\DailyTasks;

use App\Jobs\DeleteRecordedDailyTasks;
use App\Models\DailyTask;
use App\Models\Recheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class DeleteRecordedDailyTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_when_a_schedualable_is_deleted_delete_recorded_daily_tasks_is_dispatched(): void
    {
        Bus::fake();
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);

        $recheck->delete();

        Bus::assertDispatched(DeleteRecordedDailyTasks::class, fn ($job) => $job->model->is($recheck));
    }

    public function test_when_a_schedualable_is_deleted_its_recorded_tasks_are_also_deleted(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);
        $check = tap(DailyTask::factory()->make(), fn ($check) => $recheck->recordedTasks()->save($check));

        (new DeleteRecordedDailyTasks($recheck->fresh()))->handle();

        $this->assertDatabaseMissing('daily_tasks', [
            'id' => $check->id,
        ]);
    }
}
