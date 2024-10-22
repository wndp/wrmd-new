<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\Location;
use App\Models\PatientLocation;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class LocationTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;
    use CreateCase;
    use CreatesUiBehavior;

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
        $this->assertContains($admission1->patient_id, $currentPatients->pluck('patient_id'));
        $this->assertContains($admission2->patient_id, $currentPatients->pluck('patient_id'));

        PatientLocation::factory()->create([
            'patient_id' => $admission2->patient_id,
            'moved_in_at' => Carbon::now(),
        ]);

        $currentPatients = $location->fresh()->currentPatients();

        $this->assertCount(1, $currentPatients);
        $this->assertContains($admission1->patient_id, $currentPatients->pluck('patient_id'));
        $this->assertNotContains($admission2->patient_id, $currentPatients->pluck('patient_id'));
    }
}
