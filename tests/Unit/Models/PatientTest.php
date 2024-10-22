<?php

namespace Tests\Unit\Models;

use App\Enums\SettingKey;
use App\Jobs\GeocodeAddress;
use App\Jobs\PredictCircumstancesOfAdmission;
use App\Models\AttributeOption;
use App\Models\CommonName;
use App\Models\Patient;
use App\Models\Taxon;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Fluent;
use MatanYadaev\EloquentSpatial\Objects\Point;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PatientTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUiBehavior;
    use Assertions;
    use CreatesTeamUser;

    public $connectionsToTransact = ['singlestore','wildalert'];

    #[Test]
    public function aPatientHasExams(): void
    {
        $patient = Patient::factory()->make();

        $this->assertInstanceOf(Collection::class, $patient->exams);
    }

    #[Test]
    public function aPatientHasACommonNameFormattedAttribute(): void
    {
        Event::fake();

        $patient = Patient::factory()->create(['common_name' => 'Foo Bird']);

        $this->assertEquals('Foo Bird', $patient->common_name_formatted);

        Event::assertDispatched('commonNameFormatted', function ($e, $eventPatient) use ($patient) {
            return $eventPatient->id === $patient->id;
        });
    }

    #[Test]
    public function aPatientHasADaysInCareAttribute(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $patient = Patient::factory()->create([
            'disposition_id' => $pendingDispositionId, 'date_admitted_at' => Carbon::now()->subDays(10)
        ]);
        $this->assertSame(11, $patient->days_in_care);

        $patient = Patient::factory()->create([
            'disposition_id' => AttributeOption::factory(), 'date_admitted_at' => '2018-03-01', 'dispositioned_at' => '2018-03-05'
        ]);
        $this->assertSame(5, $patient->days_in_care);
    }

    #[Test]
    public function aPatientHasAnAppendedAdmittedAtForHumansAttribute(): void
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

    #[Test]
    public function aPatientIsRevisionable(): void
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

    #[Test]
    public function itDeterminesThatThePatientIsUnrecognizedIfTheTaxonIdIsNull(): void
    {
        $patient = Patient::factory()->create(['taxon_id' => null]);

        $this->assertTrue($patient->isUnrecognized());
    }

    #[Test]
    public function itDeterminesThatThePatientIsNotUnrecognizedIfTheTaxonIdIsNullAndTheCommonNameIsAnExceptedUnidetifiedName(): void
    {
        $unidentified = Arr::random(['unknown', 'void']);
        $patient = Patient::factory()->create(['taxon_id' => null, 'common_name' => $unidentified]);

        $this->assertFalse($patient->isUnrecognized());
    }

    #[Test]
    public function aPatientCanScopePatientsThatAreUnrecognizedToWrmd(): void
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

    #[Test]
    public function knownUnrecognizedPatientsAreNotScopped(): void
    {
        Patient::factory()->create(['common_name' => 'void']);
        Patient::factory()->create(['common_name' => 'UNBI']);
        Patient::factory()->create(['common_name' => 'unidentified']);
        Patient::factory()->create(['common_name' => 'unknown']);

        $result = Patient::whereUnrecognized()->get();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }

    #[Test]
    public function knownMissidentifiedPatientsAreNotScopped(): void
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

    #[Test]
    public function taxonAlphaCodesAreNotMissidentified(): void
    {
        Patient::factory()->create([
            'taxon_id' => Taxon::factory()->createQuietly(['id' => 123, 'alpha_code' => 'CAGO'])->id,
            'common_name' => 'CAGO',
        ]);

        // CommonName::factory()->createQuietly([
        //     'taxon_id' => 123,
        // ]);

        $result = Patient::whereMisidentified()->get();

        $this->assertEmpty($result);
    }

    #[Test]
    public function commonNameAlphaCodesAreNotMissidentified(): void
    {
        $taxonId = Taxon::factory()->createQuietly(['id' => 123])->id;

        Patient::factory()->create([
            'taxon_id' => $taxonId,
            'common_name' => 'CAGO',
        ]);

        $commonName = CommonName::factory()->createQuietly([
            'taxon_id' => $taxonId,
            'alpha_code' => 'CAGO',
        ]);

        $result = Patient::whereMisidentified()->get();

        $this->assertEmpty($result);
    }

    #[Test]
    public function aPatientKnowsIfItIsLocked(): void
    {
        $patient = Patient::factory()->create();
        $this->assertNull($patient->locked_at);

        $patient = Patient::factory()->create(['locked_at' => Carbon::now()]);
        $this->assertNotNull($patient->locked_at);
    }

    #[Test]
    public function ifAPatientIsLockedThenItCanNotBeUpdated(): void
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
