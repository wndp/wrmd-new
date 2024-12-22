<?php

namespace Tests\Feature\PatientNotifications;

use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\HasWildAlertEvent;
use App\Models\Taxon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class HasWildAlertEventTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_it_notifies_if_the_patient_has_been_in_care_for_excessive_days(): void
    {
        Event::fake();
        Http::fake([
            HasWildAlertEvent::apiUrl().'/alerts' => Http::response([
                'data' => [[
                    'name' => 'Foo birds with some disease',
                ]],
            ]),
        ]);

        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: [
            'taxon_id' => Taxon::factory()->create()->id,
            'disposition_id' => $pendingDispositionId,
        ]);

        tap(new HasWildAlertEvent($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Health Event'
        );
    }
}
