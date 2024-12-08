<?php

namespace Tests\Feature\DailyTasks;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\DailyTask;
use App\Models\Patient;
use App\Models\Recheck;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class RecordedPatientDailyTasksControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesTeamUser;
    use CreatesUiBehavior;

    #[Test]
    public function unAuthenticatedUsersCantRecordPatientDailyTasks(): void
    {
        $patient = Patient::factory()->create();

        $this->json('post', "internal-api/daily-tasks/record/patient/{$patient->id}")
            ->assertUnauthorized();
    }

    #[Test]
    public function unAuthorizedUsersCantRecordPatientDailyTasks(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();

        $this->actingAs($me->user)
            ->json('post', "internal-api/daily-tasks/record/patient/{$patient->id}")
            ->assertForbidden();
    }

    #[Test]
    public function itStoresANewPatientDailyTaskRecordInStorage(): void
    {
        $date = Carbon::now();
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id, 'recheck_start_at' => $date, 'recheck_end_at' => $date,
        ]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->json('post', "internal-api/daily-tasks/record/patient/{$admission->patient_id}", [
                'completed_at' => '2020-07-17 11:59:00',
                'filters' => [
                    'date' => $date,
                ],
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('daily_tasks', [
            'task_type' => Relation::getMorphAlias(Recheck::class),
            'task_id' => $recheck->id,
            'occurrence' => 1,
            'occurrence_at' => $date->format('Y-m-d'),
            'completed_at' => '2020-07-17 18:59:00',
        ]);
    }

    #[Test]
    public function itRemovesARecordedPatientDailyTaskFromStorage(): void
    {
        $singleDoseId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE
        )->attribute_option_id;

        $date = Carbon::now();
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => $date,
            'recheck_end_at' => $date,
            'frequency_id' => $singleDoseId,
        ]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $check = tap(DailyTask::factory()->make(), function ($check) use ($recheck, $date) {
            $check->occurrence = 2;
            $check->occurrence_at = $date->format('Y-m-d');
            $check->completed_at = '2020-07-17 11:59:00';
            $recheck->recordedTasks()->save($check);
        });

        $this->assertDatabaseHas('daily_tasks', [
            'task_type' => Relation::getMorphAlias(Recheck::class),
            'task_id' => $recheck->id,
            'occurrence' => 2,
            'occurrence_at' => $date->format('Y-m-d'),
            'completed_at' => '2020-07-17 11:59:00',
        ]);

        $this->actingAs($me->user)
            ->json('delete', "internal-api/daily-tasks/record/patient/{$admission->patient_id}", [
                'filters' => [
                    'date' => $date->format('Y-m-d'),
                ],
            ])
            ->assertStatus(200);

        $this->assertDatabaseMissing('daily_tasks', [
            'task_type' => Relation::getMorphAlias(Recheck::class),
            'task_id' => $recheck->id,
            'occurrence' => 2,
            'occurrence_at' => $date->format('Y-m-d'),
            'completed_at' => '2020-07-17 11:59:00',
        ]);
    }
}
