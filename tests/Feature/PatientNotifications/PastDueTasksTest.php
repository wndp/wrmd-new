<?php

namespace Tests\Feature\PatientNotifications;

use App\Enums\SettingKey;
use App\Events\NotifyPatient;
use App\Jobs\PatientNotifications\PastDueTasks;
use App\Models\Recheck;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PastDueTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_it_notifies_if_the_patient_has_past_due_tasks(): void
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

    public function test_it_does_not_notify_if_the_patient_does_not_have_past_due_tasks(): void
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
