<?php

namespace Tests\Feature\PatientNotifications;

use App\Enums\SettingKey;
use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\TasksDueToday;
use App\Models\Recheck;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class TasksDueTodayTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function itNotifiesIfThePatientHasTasksDueToday(): void
    {
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['disposition_id' => $pendingDispositionId]);
        $today = Carbon::now()->format('Y-m-d');

        $this->setSetting($me->team, SettingKey::TIMEZONE, 'UTC');

        Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => $today,
            'recheck_end_at' => $today,
        ]);
        //

        tap(new TasksDueToday($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Tasks Due!'
        );
    }

    #[Test]
    public function itDoesNotNotifyIfThePatientHasTasksDueNotToday(): void
    {
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['disposition_id' => $pendingDispositionId]);
        $tomoorow = Carbon::now()->addDay()->format('Y-m-d');

        $this->setSetting($me->team, SettingKey::TIMEZONE, 'UTC');

        Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => $tomoorow,
            'recheck_end_at' => $tomoorow,
        ]);
        //

        tap(new TasksDueToday($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertNotDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient === $admission->patient && $e->title === 'Tasks Due!'
        );
    }
}
