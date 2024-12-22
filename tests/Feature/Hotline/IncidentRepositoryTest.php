<?php

namespace Tests\Feature\Hotline;

use App\Models\Incident;
use App\Repositories\IncidentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

#[Group('hotline')]
final class IncidentRepositoryTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_it_gets_a_paginated_list_of_the_deleted_incidents(): void
    {
        $me = $this->createTeamUser();

        $notDeletedIncident = Incident::factory()->create(['team_id' => $me->team->id]);
        $deletedIncident = Incident::factory()->create(['team_id' => $me->team->id]);
        $deletedIncident->delete();

        $deleted = IncidentRepository::deletedIncidents($me->team);

        $this->assertCount(1, $deleted);
        $this->assertTrue($deleted->items()[0]->is($deletedIncident));
    }
}
