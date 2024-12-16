<?php

namespace Tests\Feature\PatientNotifications;

use App\Enums\SettingKey;
use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\PastDueTasks;
use App\Models\Recheck;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PastDueTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function itNotifiesIfThePatientHasPastDueTasks(): void
    {
        Event::fake();

        $pendingDispositionId = $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['disposition_id' => $pendingDispositionId]);
        $yestarday = Carbon::now()->subDay(2)->format('Y-m-d');

        $this->setSetting($me->team, SettingKey::TIMEZONE, 'UTC');

        Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => $yestarday,
            'recheck_end_at' => $yestarday,
        ]);

        tap(new PastDueTasks($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient->is($admission->patient) && $e->title === 'Past Due Tasks!'
        );
    }

    #[Test]
    public function itDoesNotNotifyIfThePatientDoesNotHavePastDueTasks(): void
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

        tap(new PastDueTasks($me->team, $admission->patient), fn ($class) => $class->handle());

        Event::assertNotDispatched(
            NotifyPatient::class,
            fn ($e) => $e->patient->is($admission->patient) && $e->title === 'Past Due Tasks!'
        );
    }
}
