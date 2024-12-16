<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Models\Incident;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

#[Group('hotline')]
final class ReportingPartyExportTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itExportsHotlineIncidentReportingParties(): void
    {
        Excel::fake();
        Excel::matchByRegex();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_HOTLINE->value);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_PEOPLE->value);
        BouncerFacade::allow($me->user)->to(Ability::EXPORT_PEOPLE->value);

        $reportingParty = Person::factory()->create(['team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $incident = Incident::factory()->create(['team_id' => $me->team->id, 'responder_id' => $reportingParty->id, 'reported_at' => now()]);

        $this->actingAs($me->user)
            ->from(route('hotline.open.index'))
            ->post(route('people.export'), [
                'format' => 'xlsx',
                'group' => 'reporting-parties',
                'date_from' => now()->format('Y-m-d'),
                'date_to' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('hotline.open.index'));

        Excel::assertStored('/reports\/\d+\/\w+\/hotline-reporting-parties-export\.xlsx/', 's3', function ($export) use ($reportingParty) {
            $people = $export->data()['data'];

            return $people->count() === 1 && $people->first()[0] === $reportingParty->id;
        });
    }
}
