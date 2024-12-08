<?php

namespace Tests\Feature\DailyTasks;

use App\Collections\DailyTasksCollection;
use App\Models\Recheck;
use App\Support\DailyTasksFilters;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class DailyTasksCollectionTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function dailyTaskFiltersAreRequiredToCollectDailyTasks(): void
    {
        $this->expectException(\TypeError::class);

        DailyTasksCollection::make()->withFilters([]);
    }

    #[Test]
    public function dailyTasksAreLimitedToPatientsInAnAccount(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team, 2024, [
            //'voided' => false,
            'disposition_id' => $pendingDispositionId,
        ]);

        $dailyTasks = tap(DailyTasksCollection::make(), function ($dailyTasks) use ($me) {
            $dailyTasks
                ->withFilters(new DailyTasksFilters(['facility' => 'none-assigned']))
                ->forTeam($me->team);
        });

        $this->assertSame($admission->patient_id, $dailyTasks->patientIds()->first());
    }

    #[Test]
    public function itGetsAnAccountsDailyTasks(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team, 2024, ['disposition_id' => $pendingDispositionId]);
        $date = Carbon::now()->format('Y-m-d');

        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id, 'recheck_start_at' => $date, 'recheck_end_at' => $date,
        ]);

        $dailyTasks = DailyTasksCollection::make()
            ->withFilters(new DailyTasksFilters([
                'date' => $date,
                'facility' => 'none-assigned',
            ]))
            ->forTeam($me->team);

        $this->assertSame(
            ['patients', 'category', 'slug'],
            array_keys($dailyTasks->first())
        );

        $onlyTask = $dailyTasks->first()['patients']->first()['tasks']->first();

        $this->assertSame('recheck', $onlyTask['type']);
        $this->assertSame($recheck->id, $onlyTask['type_id']);
    }

    #[Test]
    public function itGetsAPatientsDailyTasks(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();

        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team, 2024, ['disposition_id' => $pendingDispositionId]);
        $date = Carbon::now()->format('Y-m-d');

        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id, 'recheck_start_at' => $date, 'recheck_end_at' => $date,
        ]);

        $dailyTasks = DailyTasksCollection::make()
            ->withFilters(new DailyTasksFilters([
                'date' => $date,
                'facility' => 'none-assigned',
            ]))
            ->forPatient($admission->patient, $me->team);

        $this->assertSame('recheck', $dailyTasks->first()['type']);
        $this->assertSame($recheck->id, $dailyTasks->first()['type_id']);
    }

    #[Test]
    public function itGetsAPatientsPastDueDailyTasks(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();

        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team, 2024, ['disposition_id' => $pendingDispositionId]);
        $yestarday = Carbon::now()->subDay(2)->format('Y-m-d');

        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id, 'recheck_start_at' => $yestarday, 'recheck_end_at' => $yestarday,
        ]);

        $pastDue = DailyTasksCollection::make()->getPastDueForPatient($admission->patient, $me->team);

        $this->assertSame('recheck', $pastDue->first()['tasks']->first()['type']);
        $this->assertSame($recheck->id, $pastDue->first()['tasks']->first()['type_id']);
    }
}
