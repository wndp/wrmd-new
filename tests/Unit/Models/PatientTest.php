<?php

namespace Tests\Unit\Models;

use App\Enums\SettingKey;
use App\Jobs\GeocodeAddress;
use App\Jobs\PredictCircumstancesOfAdmission;
use App\Models\AttributeOption;
use App\Models\CommonName;
use App\Models\LabCbcResult;
use App\Models\LabChemistryResult;
use App\Models\LabCytologyResult;
use App\Models\LabFecalResult;
use App\Models\LabReport;
use App\Models\LabToxicologyResult;
use App\Models\LabUrinalysisResult;
use App\Models\Location;
use App\Models\Patient;
use App\Models\Taxon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use MatanYadaev\EloquentSpatial\Objects\Point;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PatientTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public $connectionsToTransact = ['singlestore', 'wildalert'];

    public function test_a_patient_has_exams(): void
    {
        $patient = Patient::factory()->make();

        $this->assertInstanceOf(Collection::class, $patient->exams);
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabReports(): void
    {
        $patient = Patient::factory()->make();

        $this->assertInstanceOf(Collection::class, $patient->labReports);
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabFecalResults(): void
    {
        $labReport = LabReport::factory()
            ->for(LabFecalResult::factory(), 'labResult')
            ->create();

        $this->assertInstanceOf(Collection::class, $labReport->patient->labFecalResults);
        $this->assertInstanceOf(LabFecalResult::class, $labReport->patient->labFecalResults->first());
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabCbcResults(): void
    {
        $labReport = LabReport::factory()
            ->for(LabCbcResult::factory(), 'labResult')
            ->create();

        $this->assertInstanceOf(Collection::class, $labReport->patient->labCbcResults);
        $this->assertInstanceOf(LabCbcResult::class, $labReport->patient->labCbcResults->first());
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabCytologyResults(): void
    {
        $labReport = LabReport::factory()
            ->for(LabCytologyResult::factory(), 'labResult')
            ->create();

        $this->assertInstanceOf(Collection::class, $labReport->patient->labCytologyResults);
        $this->assertInstanceOf(LabCytologyResult::class, $labReport->patient->labCytologyResults->first());
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabChemistryResults(): void
    {
        $labReport = LabReport::factory()
            ->for(LabChemistryResult::factory(), 'labResult')
            ->create();

        $this->assertInstanceOf(Collection::class, $labReport->patient->labChemistryResults);
        $this->assertInstanceOf(LabChemistryResult::class, $labReport->patient->labChemistryResults->first());
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabUrinalysisResults(): void
    {
        $labReport = LabReport::factory()
            ->for(LabUrinalysisResult::factory(), 'labResult')
            ->create();

        $this->assertInstanceOf(Collection::class, $labReport->patient->labUrinalysisResults);
        $this->assertInstanceOf(LabUrinalysisResult::class, $labReport->patient->labUrinalysisResults->first());
    }

    #[Test]
    #[Group('lab')]
    public function aPatientHasLabToxicologyResults(): void
    {
        $labReport = LabReport::factory()
            ->for(LabToxicologyResult::factory(), 'labResult')
            ->create();

        $this->assertInstanceOf(Collection::class, $labReport->patient->labToxicologyResults);
        $this->assertInstanceOf(LabToxicologyResult::class, $labReport->patient->labToxicologyResults->first());
    }

    public function test_a_patient_belongs_to_many_locations(): void
    {
        $patient = Patient::factory()->create();
        $locations = Location::factory()->count(3)->create();

        $patient->locations()->attach($locations);

        $this->assertInstanceOf(Collection::class, $patient->locations);
        $this->assertCount(3, $patient->locations);
        $this->assertInstanceOf(Location::class, $patient->locations->first());
    }

    public function test_a_patient_has_a_common_name_formatted_attribute(): void
    {
        Event::fake();

        $patient = Patient::factory()->create(['common_name' => 'Foo Bird']);

        $this->assertEquals('Foo Bird', $patient->common_name_formatted);

        Event::assertDispatched('commonNameFormatted', function ($e, $eventPatient) use ($patient) {
            return $eventPatient->id === $patient->id;
        });
    }

    public function test_a_patient_has_a_days_in_care_attribute(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $patient = Patient::factory()->create([
            'disposition_id' => $pendingDispositionId, 'date_admitted_at' => Carbon::now()->subDays(10),
        ]);
        $this->assertSame(11, $patient->days_in_care);

        $patient = Patient::factory()->create([
            'disposition_id' => AttributeOption::factory(), 'date_admitted_at' => '2018-03-01', 'dispositioned_at' => '2018-03-05',
        ]);
        $this->assertSame(5, $patient->days_in_care);
    }

    public function test_a_patient_has_an_appended_admitted_at_for_humans_attribute(): void
    {
        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);
        $this->setSetting($me->team, SettingKey::TIMEZONE, 'America/Los_Angeles');

        $patient = Patient::factory()->create([
            'date_admitted_at' => Carbon::parse('Mar 13 2023'),
            'time_admitted_at' => '17:13',
        ]);

        $this->assertEquals('Mar 13, 2023 10:13 am', $patient->admitted_at_for_humans);
    }

    public function test_a_patient_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Patient::factory()->create(),
            'created'
        );
    }

    // #[Test]
    // public function whenAPatientsDispositionLocationHasChangedThierCoordinatesAreGeocoded(): void
    // {
    //     Queue::fake();

    //     $patient = tap(Patient::factory()->make(), function ($patient) {
    //         $patient::$disableGeoLocation = false;
    //         $patient->update([
    //             'disposition_address' => 'the place',
    //             'disposition_county' => 'foo',
    //             'disposition_coordinates' => new Point(123.456, 987.654),
    //         ]);
    //     });

    //     $patient->disposition_address = 'another place';
    //     $patient->save();

    //     Queue::assertPushed(GeocodeAddress::class, function ($job) use ($patient) {
    //         return $job->model->id === $patient->id;
    //     });
    // }

    // #[Test]
    // public function whenAPatientsLocationFoundHasChangedThierCoordinatesAreGeocoded(): void
    // {
    //     Queue::fake();

    //     $patient = tap(Patient::factory()->make(), function ($patient) {
    //         $patient::$disableGeoLocation = false;
    //         $patient->update([
    //             'address_found' => 'the place',
    //             'county_found' => 'foo',
    //             'coordinates_found' => new Point(123.456, 987.654),
    //         ]);
    //     });

    //     $patient->address_found = 'another place';
    //     $patient->save();

    //     Queue::assertPushed(GeocodeAddress::class, function ($job) use ($patient) {
    //         return $job->model->id === $patient->id;
    //     });
    // }

    // #[Test]
    // public function whenAPatientIsCreatedItsCircumstancesOfAdmissionArePredicted(): void
    // {
    //     Queue::fake();

    //     $patient = Patient::factory()->create();

    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, function ($job) use ($patient) {
    //         return $job->patient->id === $patient->id;
    //     });
    // }

    // #[Test]
    // public function whenAPatientIsUpdatedItsCircumstancesOfAdmissionArePredicted(): void
    // {
    //     Queue::fake();

    //     $patient = Patient::factory()->create(['reasons_for_admission' => 'foo']);

    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, function ($job) use ($patient) {
    //         return $job->patient->id === $patient->id && $patient->reasons_for_admission === 'foo';
    //     });

    //     $patient->update(['disposition' => 'fake']);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, 1);

    //     $patient->update(['disposition' => 'fake', 'common_name' => 'bar']);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, 2);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, function ($job) use ($patient) {
    //         return $job->patient->id === $patient->id;
    //     });

    //     $patient->update(['disposition' => 'fake', 'reasons_for_admission' => 'bar']);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, 3);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, function ($job) use ($patient) {
    //         return $job->patient->id === $patient->id;
    //     });

    //     $patient->update(['disposition' => 'fake', 'notes_about_rescue' => 'bar']);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, 4);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, function ($job) use ($patient) {
    //         return $job->patient->id === $patient->id;
    //     });

    //     $patient->update(['disposition' => 'fake', 'care_by_rescuer' => 'bar']);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, 5);
    //     Queue::assertPushed(PredictCircumstancesOfAdmission::class, function ($job) use ($patient) {
    //         return $job->patient->id === $patient->id;
    //     });
    // }

    public function test_it_determines_that_the_patient_is_unrecognized_if_the_taxon_id_is_null(): void
    {
        $patient = Patient::factory()->create(['taxon_id' => null]);

        $this->assertTrue($patient->isUnrecognized());
    }

    public function test_it_determines_that_the_patient_is_not_unrecognized_if_the_taxon_id_is_null_and_the_common_name_is_an_excepted_unidetified_name(): void
    {
        $unidentified = Arr::random(['unknown', 'void']);
        $patient = Patient::factory()->create(['taxon_id' => null, 'common_name' => $unidentified]);

        $this->assertFalse($patient->isUnrecognized());
    }

    public function test_a_patient_can_scope_patients_that_are_unrecognized_to_wrmd(): void
    {
        $patient = Patient::factory()->create(['taxon_id' => null]);

        $result = Patient::whereUnrecognized()->get();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertSame([
            $patient->team_id,
            $patient->case_year,
            $patient->id,
        ], [
            $result->first()?->team_id,
            $result->first()?->case_year,
            $result->first()?->id,
        ]);
    }

    public function test_known_unrecognized_patients_are_not_scopped(): void
    {
        Patient::factory()->create(['common_name' => 'void']);
        Patient::factory()->create(['common_name' => 'UNBI']);
        Patient::factory()->create(['common_name' => 'unidentified']);
        Patient::factory()->create(['common_name' => 'unknown']);

        $result = Patient::whereUnrecognized()->get();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    public function test_known_missidentified_patients_are_not_scopped(): void
    {
        Patient::factory()->create(['common_name' => 'void']);
        Patient::factory()->create(['common_name' => 'UNBI']);
        Patient::factory()->create(['common_name' => 'unidentified']);
        Patient::factory()->create(['common_name' => 'unknown']);
        Patient::factory()->create(['locked_at' => Carbon::now()]);

        $result = Patient::whereMisidentified()->get();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    public function test_taxon_alpha_codes_are_not_missidentified(): void
    {
        $commonName = CommonName::factory()->create();

        Patient::factory()->create([
            'taxon_id' => Taxon::factory()->create(['id' => $commonName->taxon_id, 'alpha_code' => 'CAGO'])->id,
            'common_name' => 'CAGO',
        ]);

        $result = Patient::whereMisidentified()->get();

        $this->assertEmpty($result);
    }

    public function test_common_name_alpha_codes_are_not_missidentified(): void
    {
        $taxonId = Taxon::factory()->create()->id;

        Patient::factory()->create([
            'taxon_id' => $taxonId,
            'common_name' => 'CAGO',
        ]);

        CommonName::factory()->create([
            'taxon_id' => $taxonId,
            'alpha_code' => 'CAGO',
        ]);

        $result = Patient::whereMisidentified()->get();

        $this->assertEmpty($result);
    }

    public function test_a_patient_knows_if_it_is_locked(): void
    {
        $patient = Patient::factory()->create();
        $this->assertNull($patient->locked_at);

        $patient = Patient::factory()->create(['locked_at' => Carbon::now()]);
        $this->assertNotNull($patient->locked_at);
    }

    public function test_if_a_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create(['locked_at' => Carbon::now(), 'common_name' => 'OLD']);

        $this->assertEquals('OLD', $patient->common_name);

        $patient->update(['common_name' => 'NEW']);
        $this->assertEquals('OLD', $patient->fresh()->common_name);

        $patient->common_name = 'NEW';
        $patient->save();
        $this->assertEquals('OLD', $patient->fresh()->common_name);
    }
}
