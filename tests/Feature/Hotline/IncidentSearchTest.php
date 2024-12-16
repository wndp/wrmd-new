<?php

namespace Tests\Feature\Hotline;

use App\Actions\IncidentSearch;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Communication;
use App\Models\Incident;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('hotline')]
final class IncidentSearchTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function anIncidentSearchCanRun(): void
    {
        $results = IncidentSearch::run(
            Team::factory()->create(),
            new Request
        );

        $this->assertInstanceOf(Collection::class, $results);
    }

    #[Test]
    public function anIncidentSearchCanBeHandled(): void
    {
        $results = (new IncidentSearch)->handle(
            Team::factory()->create(),
            new Request
        );

        $this->assertInstanceOf(Collection::class, $results);
    }

    #[Test]
    public function someRequestKeysAreIgnored(): void
    {
        $hotlineStatusesId = AttributeOption::factory()->create(['name' => AttributeOptionName::HOTLINE_STATUSES])->id;

        $hotlineSearch = tap(new IncidentSearch, function ($hotlineSearch) use ($hotlineStatusesId) {
            $hotlineSearch->handle(
                Team::factory()->create(),
                new Request([
                    '_token' => 'foo',
                    '_method' => 'post',
                    'incident_status_id' => $hotlineStatusesId,
                ])
            );
        });

        $this->assertEquals(
            new Collection(['incident_status_id' => $hotlineStatusesId]),
            $hotlineSearch->arguments
        );
    }

    #[Test]
    public function itSearchesTheHotlineIncidents(): void
    {
        $team = Team::factory()->create();
        $incidents = Incident::factory()->count(2)->create([
            'team_id' => $team->id,
            'responder_id' => Person::factory()->create(['first_name' => 'Jim']),
            'recorded_by' => 'Pam',
        ])->each(function ($incident) {
            Communication::factory()->create(['incident_id' => $incident->id, 'communication' => 'lorem ipsum']);
        });

        $search = IncidentSearch::run($team, new Request([
            'first_name' => 'Jim',
            'recorded_by' => 'Pam',
            'communication' => 'lorem ipsum',
        ]));

        $this->assertCount(2, $search);
    }
}
