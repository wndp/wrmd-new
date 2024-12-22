<?php

namespace Tests\Feature\DailyTasks;

use App\Collections\DailyTasksCollection;
use App\Models\Location;
use App\Models\Recheck;
use App\Support\DailyTasksFilters;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class DailyTasksCollectionTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_daily_task_filters_are_required_to_collect_daily_tasks(): void
    {
        $this->expectException(\TypeError::class);

        DailyTasksCollection::make()->withFilters([]);
    }

    public function test_it_gets_an_accounts_daily_tasks(): void
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

    public function test_it_gets_a_patients_daily_tasks(): void
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

    public function test_it_gets_a_patients_past_due_daily_tasks(): void
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

    public function test_it_gets_daily_tasks_for_patients_in_any_location(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team, 2024, ['disposition_id' => $pendingDispositionId]);
        $date = Carbon::now()->format('Y-m-d');
        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id, 'recheck_start_at' => $date, 'recheck_end_at' => $date,
        ]);

        $location = Location::factory()->create([
            'area' => 'ICU',
            'enclosure' => 'Incubator',
        ]);
        $admission->patient->locations()->attach($location);

        $dailyTasks = DailyTasksCollection::make()
            ->withFilters(new DailyTasksFilters([
                'date' => $date,
                'facility' => 'anywhere',
            ]))
            ->forTeam($me->team);

        $patient = $dailyTasks->first()['patients']->first();

        $this->assertSame('recheck', $patient['tasks']->first()['type']);
        $this->assertSame($recheck->id, $patient['tasks']->first()['type_id']);
        $this->assertSame($location->area, $patient['area']);
        $this->assertSame($location->enclosure, $patient['enclosure']);
    }

    public function test_it_gets_daily_tasks_for_patients_in_a_specific_facility(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();
        [$clinicId] = $this->patientLocationFacilitiesIds();

        $me = $this->createTeamUser();
        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team, 2024, ['disposition_id' => $pendingDispositionId]);
        $date = Carbon::now()->format('Y-m-d');
        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id, 'recheck_start_at' => $date, 'recheck_end_at' => $date,
        ]);

        $location = Location::factory()->create([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Incubator',
        ]);
        $admission->patient->locations()->attach($location);

        $dailyTasks = DailyTasksCollection::make()
            ->withFilters(new DailyTasksFilters([
                'date' => $date,
                'facility' => $clinicId,
            ]))
            ->forTeam($me->team);

        $patient = $dailyTasks->first()['patients']->first();

        $this->assertSame('recheck', $patient['tasks']->first()['type']);
        $this->assertSame($recheck->id, $patient['tasks']->first()['type_id']);
        $this->assertSame($location->area, $patient['area']);
        $this->assertSame($location->enclosure, $patient['enclosure']);
    }
}
