<?php

namespace Tests\Feature\PatientNotifications;

use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\PatientLocked;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientLockedTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itNotifiesIfThePatientIsLocked(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: [
            'locked_at' => Carbon::now(),
        ]);

        tap(new PatientLocked($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Patient Locked'
        );
    }

    #[Test]
    public function itDoesNotNotifyIfThePatientIsNotLocked(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: [
            'locked_at' => null,
        ]);

        tap(new PatientLocked($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertNotDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Patient Locked'
        );
    }
}
