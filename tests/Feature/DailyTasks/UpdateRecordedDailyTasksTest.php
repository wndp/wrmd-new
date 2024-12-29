<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Jobs\UpdateRecordedDailyTasks;
use App\Models\DailyTask;
use App\Models\Recheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class UpdateRecordedDailyTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_when_a_schedualable_is_updated_update_recorded_daily_tasks_is_dispatched(): void
    {
        Bus::fake();

        $bidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        );

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);

        $recheck->update(['frequency_id' => $bidId]);

        Bus::assertDispatched(UpdateRecordedDailyTasks::class, fn ($job) => $job->model->is($recheck));
    }

    public function test_when_a_schedualable_is_updated_its_recorded_tasks_outside_the_window_are_deleted(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => '2022-06-21',
            'recheck_end_at' => '2022-06-22',
        ]);

        $check1 = tap(DailyTask::factory()->make([
            'occurrence' => 1,
            'occurrence_at' => '2022-06-20',
        ]), fn ($check) => $recheck->recordedTasks()->save($check));

        $check2 = tap(DailyTask::factory()->make([
            'occurrence' => 1,
            'occurrence_at' => '2022-06-23',
        ]), fn ($check) => $recheck->recordedTasks()->save($check));

        $check3 = tap(DailyTask::factory()->make([
            'occurrence' => 1,
            'occurrence_at' => '2022-06-21',
        ]), fn ($check) => $recheck->recordedTasks()->save($check));

        (new UpdateRecordedDailyTasks($recheck->fresh()))->handle();

        $this->assertDatabaseMissing('daily_tasks', ['id' => $check1->id]);
        $this->assertDatabaseMissing('daily_tasks', ['id' => $check2->id]);
        $this->assertDatabaseHas('daily_tasks', ['id' => $check3->id]);
    }

    public function test_when_a_schedualables_frequency_is_reduced_its_recorded_tasks_outside_the_window_are_deleted(): void
    {
        $bidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        );

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => '2022-06-20',
            'recheck_end_at' => '2022-06-20',
            'frequency_id' => $bidId,
        ]);

        $check1 = tap(DailyTask::factory()->make([
            'occurrence' => 1,
            'occurrence_at' => '2022-06-20',
        ]), fn ($check) => $recheck->recordedTasks()->save($check));

        $check2 = tap(DailyTask::factory()->make([
            'occurrence' => 2,
            'occurrence_at' => '2022-06-20',
        ]), fn ($check) => $recheck->recordedTasks()->save($check));

        (new UpdateRecordedDailyTasks($recheck->fresh()))->handle();

        $this->assertDatabaseHas('daily_tasks', ['id' => $check1->id]);
        $this->assertDatabaseMissing('daily_tasks', ['id' => $check2->id]);
    }
}
