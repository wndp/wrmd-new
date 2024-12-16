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
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function whenASchedualableIsUpdatedUpdateRecordedDailyTasksIsDispatched(): void
    {
        Bus::fake();

        $bidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient_id]);

        $recheck->update(['frequency_id' => $bidId]);

        Bus::assertDispatched(UpdateRecordedDailyTasks::class, fn ($job) => $job->model->is($recheck));
    }

    #[Test]
    public function whenASchedualableIsUpdatedItsRecordedTasksOutsideTheWindowAreDeleted(): void
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

    #[Test]
    public function whenASchedualablesFrequencyIsReducedItsRecordedTasksOutsideTheWindowAreDeleted(): void
    {
        $bidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

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
