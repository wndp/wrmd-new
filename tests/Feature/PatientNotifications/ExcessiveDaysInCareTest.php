<?php

namespace Tests\Feature\PatientNotifications;

use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\ExcessiveDaysInCare;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class ExcessiveDaysInCareTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public const EXESIVE_DAYS = 200;

    public const LIMITED_DAYS = 100;

    #[Test]
    public function itNotifiesIfThePatientHasBeenInCareForExcessiveDays(): void
    {
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: [
            'date_admitted_at' => Carbon::now()->subDays(self::EXESIVE_DAYS),
            'disposition_id' => $pendingDispositionId,
        ]);

        tap(new ExcessiveDaysInCare($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Days in Care'
        );
    }

    #[Test]
    public function it_does_not_notify_if_the_patient_has_been_in_care_for_limited_days(): void
    {
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: [
            'date_admitted_at' => Carbon::now()->subDays(self::LIMITED_DAYS),
            'disposition_id' => $pendingDispositionId,
        ]);

        tap(new ExcessiveDaysInCare($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertNotDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Days in Care'
        );
    }
}
