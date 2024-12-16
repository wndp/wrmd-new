<?php

namespace Tests\Feature\PatientNotifications;

use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\MultipleCollaborators;
use App\Models\Admission;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class MultipleCollaboratorsTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itNotifiesIfThereAMultipleCollaborators(): void
    {
        Event::fake();
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);

        Admission::factory()->create([
            'patient_id' => $admission->patient->id,
            'case_year' => Carbon::now()->format('Y')
        ]);

        tap(new MultipleCollaborators($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Multiple Collaborators!'
        );
    }

    #[Test]
    public function itDoesNotNotifyfThereAMultipleCollaborators(): void
    {
        Event::fake();
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);

        tap(new MultipleCollaborators($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertNotDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Multiple Collaborators!'
        );
    }
}
