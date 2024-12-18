<?php

namespace Tests\Unit\Models;

use App\Models\Location;
use App\Models\Patient;
use App\Models\PatientLocation;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesUiBehavior;

final class LocationTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesUiBehavior;
    use RefreshDatabase;

    // #[Test]
    // public function append_accessors_to_the_location_model(): void
    // {
    //     $me = $this->createTeamUser();
    //     $this->setSetting($me->account, 'date_format', 'Y-m-d');

    //     $location = Location::factory()->make()->toArray();

    //     $this->assertArrayHasKey('location', $location);
    // }

    // #[Test]
    // public function a_location_has_formatted_location(): void
    // {
    //     $location = Location::factory()->make([
    //         'area' => 'ICU',
    //         'enclosure' => 'Inc 1',
    //     ]);

    //     $this->assertEquals('ICU, Inc 1', $location->location);

    //     $location = Location::factory()->make([
    //         'area' => 'ICU',
    //         'enclosure' => '',
    //     ]);

    //     $this->assertEquals('ICU', $location->location);
    // }

    #[Test]
    public function a_location_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Location::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function a_location_belongs_to_a_team(): void
    {
        $this->assertInstanceOf(Team::class, Location::factory()->create()->team);
    }

    #[Test]
    public function a_location_belongs_to_many_patients(): void
    {
        $location = Location::factory()->create();
        $patients = Patient::factory()->count(3)->create();

        $location->patients()->attach($patients);

        $this->assertInstanceOf(Collection::class, $location->patients);
        $this->assertCount(3, $location->patients);
        $this->assertInstanceOf(Patient::class, $location->patients->first());
    }

    #[Test]
    public function a_location_knows_its_current_patients(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();

        $team = Team::factory()->create();
        $location = Location::factory()->create(['team_id' => $team->id]);

        $admission1 = $this->createCase($team, patientOverrides: ['disposition_id' => $pendingDispositionId]);
        PatientLocation::factory()->create([
            'patient_id' => $admission1->patient_id,
            'location_id' => $location->id,
            'moved_in_at' => Carbon::now()->subDay(),
        ]);

        $admission2 = $this->createCase($team, patientOverrides: ['disposition_id' => $pendingDispositionId]);
        PatientLocation::factory()->create([
            'patient_id' => $admission2->patient_id,
            'location_id' => $location->id,
            'moved_in_at' => Carbon::now()->subDay(),
        ]);

        $currentPatients = $location->currentPatients();

        $this->assertCount(2, $currentPatients);
        $this->assertContains($admission1->patient_id, $currentPatients->pluck('id'));
        $this->assertContains($admission2->patient_id, $currentPatients->pluck('id'));
    }

    #[Test]
    public function aLocationHasAFormattedLocation(): void
    {
        [$clinicId, $homeCareId] = $this->patientLocationFacilitiesIds();

        $location = Location::factory()->make([
            'facility_id' => $clinicId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $this->assertEquals('ICU, Inc 1', $location->location_for_humans);

        $location = Location::factory()->make([
            'facility_id' => $homeCareId,
            'area' => 'Rachel Avilla',
            'enclosure' => '123 main st',
        ]);

        $this->assertEquals('Rachel Avilla', $location->location_for_humans);
    }
}
