<?php

namespace Tests\Unit\Models;

use App\Models\Admission;
use App\Models\AttributeOption;
use App\Models\Patient;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesUiBehavior;

final class AdmissionTest extends TestCase
{
    use CreateCase;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_an_admission_belongs_to_a_team(): void
    {
        $admission = $this->createCase();

        $this->assertInstanceOf(Team::class, $admission->team);
    }

    public function test_an_admission_belongs_to_an_patient(): void
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

    public function test_an_admission_has_an_appended_case_number_attribute(): void
    {
        $admission = $this->createCase(caseYear: 2018);
        $this->assertEquals('18-1', $admission->case_number);

        $admission = $this->createCase(caseYear: 2018);

        Event::listen('caseNumberFormatted', function ($admission) {
            return "$admission->team_id::$admission->case_year";
        });

        $this->assertEquals("$admission->team_id::2018", $admission->case_number);
    }

    public function test_it_gets_the_admissions_with_a_disposition_date_that_were_in_care_on_a_given_date(): void
    {
        $team = Team::factory()->create();

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => '2018-01-02', 'disposition_id' => AttributeOption::factory(),
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => '2018-01-03', 'disposition_id' => AttributeOption::factory(),
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-02', 'dispositioned_at' => '2018-01-04', 'disposition_id' => AttributeOption::factory(),
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-03', 'dispositioned_at' => '2018-01-03', 'disposition_id' => AttributeOption::factory(),
        ]);

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-04', 'dispositioned_at' => '2018-01-04', 'disposition_id' => AttributeOption::factory(),
        ]);

        $this->assertCount(3, Admission::inCareOnDate($team, Carbon::createFromDate(2018, 1, 3)));
    }

    public function test_it_gets_the_admissions_without_a_disposition_date_that_were_admitted_on_or_before_a_given_date(): void
    {
        $team = Team::factory()->create();

        $pendingDispositionId = $this->pendingDispositionId();

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId,
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-02', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId,
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-03', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId,
        ]);

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-04', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId,
        ]);

        $this->assertCount(3, Admission::inCareOnDate($team, Carbon::createFromDate(2018, 1, 3)));
    }

    public function test_it_gets_the_admissions_without_a_final_disposition_that_were_in_care_on_a_given_date(): void
    {
        $team = Team::factory()->create();

        $pendingDispositionId = $this->pendingDispositionId();

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => null, 'disposition_id' => $pendingDispositionId,
        ]);

        // false
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-01', 'dispositioned_at' => null, 'disposition_id' => AttributeOption::factory(),
        ]);

        // true
        $this->createCase($team, patientOverrides: [
            'date_admitted_at' => '2018-01-02', 'dispositioned_at' => '2018-01-04', 'disposition_id' => AttributeOption::factory(),
        ]);

        $this->assertCount(2, Admission::inCareOnDate($team, Carbon::createFromDate(2018, 1, 3)));
    }

    public function test_it_gets_the_last_case_id_used(): void
    {
        $team = Team::factory()->create();

        Admission::factory()->count(5)->create(['team_id' => $team->id, 'case_year' => date('Y')]);

        $results = Admission::getLastCaseId($team->id, date('Y'));

        $this->assertEquals(5, $results);
    }

    public function test_it_gets_the_years_used_in_an_account(): void
    {
        $team = Team::factory()->create();

        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y')]);
        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y') - 1]);

        $results = Admission::yearsInAccount($team->id);

        $this->assertSame([date('Y') - 0, date('Y') - 1], $results->toArray());
    }

    public function test_missing_years_are_filled_in(): void
    {
        $team = Team::factory()->create();

        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y')]);
        Admission::factory()->create(['team_id' => $team->id, 'case_year' => date('Y') - 2]);

        $results = Admission::yearsInAccount($team->id);

        $this->assertSame([date('Y') - 0, date('Y') - 1, date('Y') - 2], $results->toArray());
    }
}
