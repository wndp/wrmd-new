<?php

namespace Tests\Unit\Models;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use App\Models\AttributeOption;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesUiBehavior;

final class AdmissionTest extends TestCase
{
    use CreateCase;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function anAdmissionBelongsToATeam(): void
    {
        $admission = $this->createCase();

        $this->assertInstanceOf(Team::class, $admission->team);
    }

    #[Test]
    public function anAdmissionBelongsToAnPatient(): void
    {
        $admission = $this->createCase();

        $this->assertInstanceOf(Patient::class, $admission->patient);
    }

    // #[Test]
    // public function anAdmissionCanScopePatientsThatAreMissidentifiedToWrmd(): void
    // {
    //     $admission = $this->createCase();

    //     $result = Admission::select('admissions.*')->whereMisidentified()->get();

    //     $this->assertSame([
    //         $admission->team_id,
    //         $admission->case_year,
    //         $admission->id,
    //     ], [
    //         $result->first()?->team_id,
    //         $result->first()?->case_year,
    //         $result->first()?->id,
    //     ]);
    // }

    // #[Test]
    // public function anAdmissionCanScopePatientsThatAreUnrecognizedToWrmd(): void
    // {
    //     $admission = $this->createCase();
    //     $admission->patient->forceFill(['taxon_id' => null])->save();

    //     $result = Admission::select('admissions.*')->whereUnrecognized()->get();

    //     $this->assertSame([
    //         $admission->team_id,
    //         $admission->case_year,
    //         $admission->id,
    //     ], [
    //         $result->first()?->team_id,
    //         $result->first()?->case_year,
    //         $result->first()?->id,
    //     ]);
    // }

    #[Test]
    public function anAdmissionHasAnAppendedCaseNumberAttribute(): void
    {
        $admission = $this->createCase(caseYear: 2018);
        $this->assertEquals('18-1', $admission->case_number);

        $admission = $this->createCase(caseYear: 2018);

        Event::listen('caseNumberFormatted', function ($admission) {
            return "$admission->team_id::$admission->case_year";
        });

        $this->assertEquals("$admission->team_id::2018", $admission->case_number);
    }

    #[Test]
    public function itGetsTheAdmissionsWithADispositionDateThatWereInCareOnAGivenDate(): void
    {
        $team = Team::factory()->create();

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => '2018-01-02', 'disposition_id' => AttributeOption::factory()
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => '2018-01-03', 'disposition_id' => AttributeOption::factory()
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-02', 'dispositioned_at' => '2018-01-04', 'disposition_id' => AttributeOption::factory()
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-03', 'dispositioned_at' => '2018-01-03', 'disposition_id' => AttributeOption::factory()
        ]);

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-04', 'dispositioned_at' => '2018-01-04', 'disposition_id' => AttributeOption::factory()
        ]);

        $this->assertCount(3, Admission::inCareOnDate($team, Carbon::createFromDate(2018, 1, 3)));
    }

    #[Test]
    public function itGetsTheAdmissionsWithoutADispositionDateThatWereAdmittedOnOrBeforeAGivenDate(): void
    {
        $team = Team::factory()->create();

        $pendingDispositionId = $this->pendingDispositionId();

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-02', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-03', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId
        ]);

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-04', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId
        ]);

        $this->assertCount(3, Admission::inCareOnDate($team, Carbon::createFromDate(2018, 1, 3)));
    }

    #[Test]
    public function itGetsTheAdmissionsWithoutAFinalDispositionThatWereInCareOnAGivenDate(): void
    {
        $team = Team::factory()->create();

        $pendingDispositionId = $this->pendingDispositionId();

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId
        ]);

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => null, 'disposition_id' => AttributeOption::factory()
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-02', 'dispositioned_at' => '2018-01-04', 'disposition_id' => AttributeOption::factory()
        ]);

        $this->assertCount(2, Admission::inCareOnDate($team, Carbon::createFromDate(2018, 1, 3)));
    }

    #[Test]
    public function itGetsTheLastCaseIdUsed(): void
    {
        $team = Team::factory()->create();

        Admission::factory()->count(5)->create(['team_id' => $team->id, 'case_year' => date('Y')]);

        $results = Admission::getLastCaseId($team->id, date('Y'));

        $this->assertEquals(5, $results);
    }

    #[Test]
    public function itGetsTheYearsUsedInAnAccount(): void
    {
        $team = Team::factory()->create();

        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y')]);
        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y') - 1]);

        $results = Admission::yearsInAccount($team->id);

        $this->assertSame([date('Y') - 0, date('Y') - 1], $results->toArray());
    }

    #[Test]
    public function missingYearsAreFilledIn(): void
    {
        $team = Team::factory()->create();

        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y')]);
        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y') - 2]);

        $results = Admission::yearsInAccount($team->id);

        $this->assertSame([date('Y') - 0, date('Y') - 1, date('Y') - 2], $results->toArray());
    }
}
