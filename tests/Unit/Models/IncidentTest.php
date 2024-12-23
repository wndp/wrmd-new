<?php

namespace Tests\Unit\Models;

use App\Jobs\GeocodeAddress;
use App\Models\Incident;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use MatanYadaev\EloquentSpatial\Objects\Point;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('hotline')]
final class IncidentTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_an_incident_belongs_to_an_team(): void
    {
        $this->assertInstanceOf(Team::class, Incident::factory()->create()->team);
    }

    public function test_an_incident_belongs_to_a_reporting_party(): void
    {
        $this->assertInstanceOf(Person::class, Incident::factory()->create()->reportingParty);
    }

    public function test_an_auto_incrementing_incident_number_is_determined_based_on_the_current_year(): void
    {
        $team1Id = Team::factory()->create()->id;
        $team2Id = Team::factory()->create()->id;

        // The first incident
        $incident1 = Incident::factory()->create(['team_id' => $team1Id]);

        // The second incident
        $incident2 = Incident::factory()->create(['team_id' => $team1Id]);

        // Incrementing is unique to each team
        $incident3 = Incident::factory()->create(['team_id' => $team2Id]);

        $this->assertSame('HL-'.date('y').'-0001', $incident1->incident_number);
        $this->assertSame('HL-'.date('y').'-0002', $incident2->incident_number);
        $this->assertSame('HL-'.date('y').'-0001', $incident3->incident_number);
    }

    public function test_incident_numbers_will_increment_if_any_previous_incidents_are_deleted(): void
    {
        $teamId = Team::factory()->create()->id;

        $incident1 = Incident::factory()->create(['team_id' => $teamId]);
        $incident1->delete();

        $incident2 = Incident::factory()->create(['team_id' => $teamId]);

        $this->assertSame('HL-'.date('y').'-0001', $incident1->incident_number);
        $this->assertSame('HL-'.date('y').'-0002', $incident2->incident_number);
    }

    public function test_an_incident_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Incident::factory()->create(),
            'created'
        );
    }

    // #[Test]
    // public function whenAnIncidentsLocationHasChangedItsCoordinatesAreGeocoded(): void
    // {
    //     Queue::fake();

    //     $incident = Incident::factory()->create([
    //         'incident_city' => 'the place',
    //         'incident_coordinates' => new Point(123.456, 987.654),
    //     ]);

    //     $incident->city = 'another place';
    //     $incident->save();

    //     Queue::assertPushed(GeocodeAddress::class, function ($job) use ($incident) {
    //         return $job->model->id === $incident->id;
    //     });
    // }
}
