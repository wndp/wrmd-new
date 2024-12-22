<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\Location;
use App\Models\Patient;
use App\Models\PatientLocation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
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

    public function test_a_patient_location_belongs_to_a_patient(): void
    {
        $this->assertInstanceOf(Patient::class, PatientLocation::factory()->create()->patient);
    }

    public function test_a_patient_location_belongs_to_a_location(): void
    {
        $this->assertInstanceOf(Location::class, PatientLocation::factory()->create()->location);
    }

    public function test_a_patient_location_has_summary_body_attribute(): void
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

    public function test_patient_locations_are_filtered_into_the_care_log(): void
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

    public function test_a_patient_location_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            PatientLocation::factory()->create(),
            'created'
        );
    }

    public function test_if_a_patient_locations_patient_is_locked_then_it_can_not_be_updated(): void
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
            'comments' => 'NEW',
        ]);
        $this->assertEquals('OLD', $patient->locations->first()->patientLocation->fresh()->comments);

        // Cant save
        $patientLocation->comments = 'NEW';
        $patientLocation->save();
        $this->assertEquals('OLD', $patientLocation->fresh()->comments);
    }

    public function test_if_a_patient_locations_patient_is_locked_then_it_can_not_be_created(): void
    {
        $patientLocation = PatientLocation::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($patientLocation->exists);
    }

    public function test_if_a_patient_locations_patient_is_locked_then_it_can_not_be_deleted(): void
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
