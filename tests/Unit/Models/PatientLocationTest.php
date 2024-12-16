<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\Location;
use App\Models\Patient;
use App\Models\PatientLocation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PatientLocationTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use GetsCareLogs;
    use RefreshDatabase;

    public function aPatientLocationBelongsToAPatient(): void
    {
        $this->assertInstanceOf(Patient::class, PatientLocation::factory()->create()->patient);
    }

    public function aPatientLocationBelongsToALocation(): void
    {
        $this->assertInstanceOf(Location::class, PatientLocation::factory()->create()->patient);
    }

    #[Test]
    public function aPatientLocationHasFormattedLocation(): void
    {
        [$clinicId, $homeCareId] = $this->patientLocationFacilitiesIds();

        $patientLocation = PatientLocation::factory()->make([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $this->assertEquals('ICU, Inc 1', $patientLocation->location_for_humans);

        $patientLocation = PatientLocation::factory()->make([
            'facility_id' => $homeCareId,
            'area' => 'Rachel Avilla',
            'enclosure' => '123 main st',
        ]);

        $this->assertEquals('Rachel Avilla', $patientLocation->location_for_humans);
    }

    #[Test]
    public function aPatientLocationHasSummaryBodyAttribute(): void
    {
        [$clinicId, $homeCareId] = $this->patientLocationFacilitiesIds();

        $location = PatientLocation::factory()->make([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
            'comments' => 'foo bar',
        ]);

        $this->assertEquals('Moved to ICU, Inc 1. foo bar', $location->summary_body);
    }

    #[Test]
    public function patientLocationsAreFilteredIntoTheCareLog(): void
    {
        [$clinicId, $homeCareId] = $this->patientLocationFacilitiesIds();

        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);
        $patient = Patient::factory()->create();

        PatientLocation::factory()->create([
            'patient_id' => $patient->id,
            'moved_in_at' => '2017-04-09',
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
            'comments' => 'foo bar',
        ]);

        $logs = $this->getCareLogs(
            $patient,
            $me->user
        );

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(PatientLocation::class, $logs[0]->model);
        $this->assertEquals('2017-04-08 17:00:00', $logs[0]->logged_at_date_time->toDateTimeString());
        //$this->assertEquals('Moved to ICU, Inc 1. foo bar', $logs[0]->body);
    }

    #[Test]
    public function aLocationIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            PatientLocation::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifAPatientLocationsPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $patient->id, 'area' => 'OLD']);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $patientLocation->patient->refresh();

        // Cant update
        $patientLocation->update(['area' => 'NEW']);
        $this->assertEquals('OLD', $patientLocation->fresh()->area);

        // Cant save
        $patientLocation->area = 'NEW';
        $patientLocation->save();
        $this->assertEquals('OLD', $patientLocation->fresh()->area);
    }

    #[Test]
    public function ifAPatientLocationsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $patientLocation = PatientLocation::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($patientLocation->exists);
    }

    #[Test]
    public function ifAPatientLocationsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $patientLocation->patient->refresh();

        $patientLocation->delete();
        $this->assertDatabaseHas('patient_locations', ['id' => $patientLocation->id, 'deleted_at' => null]);
    }
}
