<?php

namespace Tests\Feature\Analytics;

use App\Analytics\AnalyticFilters;
use App\Analytics\AnalyticFiltersStore;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\CommonName;
use App\Models\Taxon;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class NumbersTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    #[Test]
    public function itGetsTheNumberOfCasesForThisYear(): void
    {
        $team = Team::factory()->create();
        $thisYear = date('Y');
        $lastYear = $thisYear - 1;
        $pendingDispositionId = $this->pendingDispositionId();

        $this->createCase($team, $thisYear, ['date_admitted_at' => date('Y-m-d')]);
        $this->createCase($team, $lastYear, ['date_admitted_at' => $lastYear.date('-m-d'), 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $lastYear, ['date_admitted_at' => $lastYear.date('-m-d'), 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $lastYear, ['date_admitted_at' => $lastYear.date('-m-d', strtotime('+1 day')), 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\PatientsAdmitted::analyze(
            $team,
            new AnalyticFilters(AnalyticFiltersStore::all()->merge([
                'segments' => ['All Patients'],
                'date_from' => "$thisYear-01-01",
                'date_to' => $thisYear.date('-m-d'),
                'compare' => true,
                'compare_date_from' => "$lastYear-01-01",
                'compare_date_to' => $lastYear.date('-m-d'),
            ]))
        );

        $this->assertEquals(50.0, $result->difference);
        $this->assertEquals('down', $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(2, $result->prev);
    }

    #[Test]
    public function itGetsTheNumberOfCasesForThisWeekToDate(): void
    {
        $team = Team::factory()->create();
        $thisYear = now()->format('Y');
        $today = now()->format('Y-m-d');
        $sevenDaysAgo = now()->subDays(7)->format('Y-m-d');
        $fourteenDaysAgo = now()->subDays(14)->format('Y-m-d');
        $pendingDispositionId = $this->pendingDispositionId();

        $this->createCase($team, $thisYear, ['date_admitted_at' => $today, 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $thisYear, ['date_admitted_at' => $sevenDaysAgo, 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $thisYear, ['date_admitted_at' => $sevenDaysAgo, 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $thisYear, ['date_admitted_at' => $fourteenDaysAgo, 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\PatientsAdmitted::analyze(
            $team,
            new AnalyticFilters(AnalyticFiltersStore::all()->merge([
                'segments' => ['All Patients'],
                'date_from' => now()->startOfWeek(),
                'date_to' => now(),
                'compare' => true,
                'compare_date_from' => now()->subWeek()->startOfWeek(),
                'compare_date_to' => now()->subWeek(),
            ]))
        );

        $this->assertEquals(50.0, $result->difference);
        $this->assertEquals('down', $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(2, $result->prev);
    }

    #[Test]
    public function itGetsTheNumberOfPatientsInCare(): void
    {
        $team = Team::factory()->create();
        $thisYear = now()->format('Y');
        $pendingDispositionId = $this->pendingDispositionId();
        $releasedDispositionId = $this->createUiBehavior(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED
        )->attribute_option_id;

        $this->createCase($team, $thisYear, ['disposition_id' => $pendingDispositionId,
            'dispositioned_at' => null, 'date_admitted_at' => now(),
        ]);
        $this->createCase($team, $thisYear, ['disposition_id' => $releasedDispositionId,
            'dispositioned_at' => now()->subDay(), 'date_admitted_at' => now()->subDay(),
        ]);
        $this->createCase($team, $thisYear, ['disposition_id' => $releasedDispositionId,
            'dispositioned_at' => now()->subDay(), 'date_admitted_at' => now()->subDay(),
        ]);

        $result = \App\Analytics\Numbers\PatientsInCare::analyze(
            $team,
            new AnalyticFilters([
                'compare' => true,
            ])
        );

        $this->assertEquals(50, $result->difference);
        $this->assertEquals('down', $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(2, $result->prev);
    }

    #[Test]
    public function itGetsTheTotalNumberOfPatientsAdmitted(): void
    {
        $this->createCase(Team::factory()->create(), 2018);
        $this->createCase(Team::factory()->create(), 2017);

        $result = \App\Analytics\Numbers\AllPatientsAdmitted::analyze(
            Team::factory()->create(),
            new AnalyticFilters(AnalyticFiltersStore::all()->merge([
                'segments' => ['All Patients'],
                'date_period' => 'all-dates',
            ]))
        );

        $this->assertEquals(null, $result->difference);
        $this->assertEquals(null, $result->change);
        $this->assertEquals(2, $result->now);
        $this->assertEquals(null, $result->prev);
    }

    #[Test]
    public function itGetsTheTotalNumberOfPatientsAdmittedThisWeek(): void
    {
        $team = Team::factory()->create();
        $thisYear = now()->format('Y');
        $today = now()->format('Y-m-d');
        $sevenDaysAgo = now()->subDays(7)->format('Y-m-d');
        $fourteenDaysAgo = now()->subDays(14)->format('Y-m-d');
        $pendingDispositionId = $this->pendingDispositionId();

        $this->createCase($team, $thisYear, ['date_admitted_at' => $today, 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $thisYear, ['date_admitted_at' => $sevenDaysAgo, 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, $thisYear, ['date_admitted_at' => $fourteenDaysAgo, 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\AllPatientsAdmitted::analyze(
            Team::factory()->create(),
            new AnalyticFilters(AnalyticFiltersStore::all()->merge([
                'segments' => ['All Patients'],
                'admitted_from' => now()->startOfWeek(),
                'admitted_to' => now(),
            ]))
        );

        $this->assertEquals(null, $result->difference);
        $this->assertEquals(null, $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(null, $result->prev);
    }

    #[Test]
    public function itGetsTheTotalNumberOfAllUnrecognizedPatients(): void
    {
        // Disable AttemptSpeciesIdentification
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();
        $taxon = Taxon::factory()->create();
        $this->createCase(Team::factory()->create(), 2024, ['taxon_id' => null, 'disposition_id' => $pendingDispositionId]);
        $this->createCase(Team::factory()->create(), 2024, ['taxon_id' => $taxon->id, 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\AllUnrecognizedPatients::analyze(
            Team::factory()->create(),
            new AnalyticFilters()
        );

        $this->assertEquals(null, $result->difference);
        $this->assertEquals(null, $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(null, $result->prev);
    }

    #[Test]
    public function itGetsTheNumberOfUnrecognizedPatients(): void
    {
        // Disable AttemptSpeciesIdentification
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();
        $team = Team::factory()->create();
        $taxon = Taxon::factory()->create();

        $this->createCase(Team::factory()->create(), 2024, ['taxon_id' => null]);
        $this->createCase($team, 2024, ['taxon_id' => null, 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, 2024, ['taxon_id' => $taxon->id, 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\UnrecognizedPatients::analyze(
            $team,
            new AnalyticFilters()
        );

        $this->assertEquals(null, $result->difference);
        $this->assertEquals(null, $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(null, $result->prev);
    }

    #[Test]
    public function itGetsTheTotalNumberOfAllMisidentifiedPatients(): void
    {
        // Disable AttemptSpeciesIdentification
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();

        $taxon = Taxon::factory()->create();
        CommonName::factory()->create(['common_name' => 'Big Bat', 'taxon_id' => $taxon->id]);

        $this->createCase(Team::factory()->create(), 2024, ['taxon_id' => $taxon->id, 'common_name' => 'Big Bat', 'disposition_id' => $pendingDispositionId]);
        $this->createCase(Team::factory()->create(), 2024, ['taxon_id' => $taxon->id, 'common_name' => 'Foo Bird', 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\AllMissidentifiedPatients::analyze(
            Team::factory()->create(),
            new AnalyticFilters()
        );

        $this->assertEquals(null, $result->difference);
        $this->assertEquals(null, $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(null, $result->prev);
    }

    #[Test]
    public function itGetsTheNumberOfMisidentifiedPatients(): void
    {
        // Disable AttemptSpeciesIdentification
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();
        $team = Team::factory()->create();
        $taxon = Taxon::factory()->create();
        CommonName::factory()->create(['common_name' => 'Big Bat', 'taxon_id' => $taxon->id]);

        $this->createCase(Team::factory()->create(), 2024, ['taxon_id' => $taxon->id, 'common_name' => 'Foo Bird']);
        $this->createCase($team, 2024, ['taxon_id' => $taxon->id, 'common_name' => 'Big Bat', 'disposition_id' => $pendingDispositionId]);
        $this->createCase($team, 2024, ['taxon_id' => $taxon->id, 'common_name' => 'Foo Bird', 'disposition_id' => $pendingDispositionId]);

        $result = \App\Analytics\Numbers\MissidentifiedPatients::analyze(
            $team,
            new AnalyticFilters()
        );

        $this->assertEquals(null, $result->difference);
        $this->assertEquals(null, $result->change);
        $this->assertEquals(1, $result->now);
        $this->assertEquals(null, $result->prev);
    }
}
