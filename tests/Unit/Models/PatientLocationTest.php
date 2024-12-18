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

    #[Test]
    public function aPatientLocationBelongsToAPatient(): void
    {
        $this->assertInstanceOf(Patient::class, PatientLocation::factory()->create()->patient);
    }

    #[Test]
    public function aPatientLocationBelongsToALocation(): void
    {
        $this->assertInstanceOf(Location::class, PatientLocation::factory()->create()->location);
    }

    #[Test]
    public function aPatientLocationHasSummaryBodyAttribute(): void
    {
        [$clinicId, $homeCareId] = $this->patientLocationFacilitiesIds();

        $patient = Patient::factory()->create();

        $location = Location::factory()->create([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $x = $patient->locations()->attach($location, [
            'moved_in_at' => '2017-04-09',
            'comments' => 'foo bar',
        ]);
        //dd($patient->locations->first()->patientLocation);

        $this->assertEquals('Moved to ICU, Inc 1. foo bar', $patient->locations->first()->patientLocation->summary_body);
    }

    #[Test]
    public function patientLocationsAreFilteredIntoTheCareLog(): void
    {
        [$clinicId] = $this->patientLocationFacilitiesIds();

        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);
        $patient = Patient::factory()->create();

        $location = Location::factory()->create([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $patient->locations()->attach($location, [
            'moved_in_at' => '2017-04-09',
            'comments' => 'foo bar',
        ]);

        $logs = $this->getCareLogs($patient, $me->user);

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(PatientLocation::class, $logs[0]->model);
        $this->assertEquals('2017-04-08 17:00:00', $logs[0]->logged_at_date_time->toDateTimeString());
        //$this->assertEquals('Moved to ICU, Inc 1. foo bar', $logs[0]->body);
    }

    #[Test]
    public function aPatientLocationIsRevisionable(): void
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
        [$clinicId] = $this->patientLocationFacilitiesIds();

        $patient = Patient::factory()->create();
        $location = Location::factory()->create([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $patient->locations()->attach($location, [
            'comments' => 'OLD',
        ]);

        $patient->locked_at = Carbon::now();
        $patient->save();
        //$patientLocation->patient->refresh();

        $patientLocation = $patient->locations()->first()->patientLocation;

        // Cant update
        $patient->locations()->updateExistingPivot($patientLocation->id, [
            'comments' => 'NEW'
        ]);
        $this->assertEquals('OLD', $patient->locations->first()->patientLocation->fresh()->comments);

        // Cant save
        $patientLocation->comments = 'NEW';
        $patientLocation->save();
        $this->assertEquals('OLD', $patientLocation->fresh()->comments);
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
