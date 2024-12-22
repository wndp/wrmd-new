<?php

namespace Tests\Feature\PatientNotifications;

use App\Models\Patient;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
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

    public function test_it_validates_ownership_of_the_patient_before_sending_notifications(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();

        $this->actingAs($me->user)
            ->get(route('patients.notifications', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_a_batch_of_jobs_is_queued_for_the_patient(): void
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

    public function test_patient_notifications_are_not_sent_if_they_were_already_recently_sent(): void
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
