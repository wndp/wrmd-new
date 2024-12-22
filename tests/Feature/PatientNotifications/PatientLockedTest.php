<?php

namespace Tests\Feature\PatientNotifications;

use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\PatientLocked;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientLockedTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_it_notifies_if_the_patient_is_locked(): void
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

    public function test_it_does_not_notify_if_the_patient_is_not_locked(): void
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
