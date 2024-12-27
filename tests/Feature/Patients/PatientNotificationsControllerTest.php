<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class PatientNotificationsControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_it_validates_ownership_of_the_patient_before_sending_notifications(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();

        $this->actingAs($me->user)
            ->get(route('patients.notifications', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_a_batch_of_jobs_is_queued_for_the_patient(): void
    {
        Bus::fake();

        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);

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

        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);

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
