<?php

namespace Tests\Feature\PatientNotifications;

use App\Models\Patient;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientNotificationsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itValidatesOwnershipOfThePatientBeforeSendingNotifications(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();

        $this->actingAs($me->user)
            ->get(route('patients.notifications', $patient))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aBatchOfJobsIsQueuedForThePatient(): void
    {
        Bus::fake();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->get(route('patients.notifications', $admission->patient));

        Bus::assertBatched(function (PendingBatch $batch) {
            return $batch->name == 'Patient Notifications';
        });
    }

    #[Test]
    public function patientNotificationsAreNotSentIfTheyWereAlreadyRecentlySent(): void
    {
        Bus::fake();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);

        // Patient Notifications were sent
        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->get(route('patients.notifications', $admission->patient))
            ->assertExactJson([1]);

        // Patient Notifications were not sent
        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->get(route('patients.notifications', $admission->patient))
            ->assertExactJson([0]);
    }
}
