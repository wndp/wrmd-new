<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Patients\TreatmentLog;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class TreatmentLogControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_store_a_treatment_log(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.treatment_log.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_a_treatment_log(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.treatment_log.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_patient_before_storing(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->post(route('patients.treatment_log.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_a_treatment_log(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->post(route('patients.treatment_log.store', $admission->patient))
            ->assertHasValidationError('treated_at', 'The treated at date field is required.')
            ->assertHasValidationError('weight', 'The weight field is required when comments is not present.')
            ->assertHasValidationError('comments', 'The comments field is required when weight is not present.');

        $this->actingAs($me->user)
            ->post(route('patients.treatment_log.store', $admission->patient), [
                'treated_at' => 'foo',
                'weight' => 'foo',
            ])
            ->assertHasValidationError('treated_at', 'The treated at date is not a valid date.')
            ->assertHasValidationError('weight', 'The weight must be a number.');
    }

    public function test_it_stores_a_treatment_log(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.treatment_log.store', $admission->patient), [
                'treated_at' => '2022-07-01',
                'weight' => '123',
                'weight_unit' => 'gm',
                'comments' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('treatment_logs', [
            'patient_id' => $admission->patient_id,
            'treated_at' => '2022-07-01 00:00',
            'weight' => '123.00',
            'weight_unit' => 'gm',
            'comments' => 'lorem',
        ]);
    }

    public function test_it_validates_ownership_of_a_treatment_log_before_updating(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $treatmentLog = TreatmentLog::factory()->create();
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->put(route('patients.treatment_log.update', [$patient, $treatmentLog]))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_treatment_log(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $treatmentLog = TreatmentLog::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->put(route('patients.treatment_log.update', [$admission->patient, $treatmentLog]))
            ->assertHasValidationError('treated_at', 'The treated at date field is required.')
            ->assertHasValidationError('weight', 'The weight field is required when comments is not present.')
            ->assertHasValidationError('comments', 'The comments field is required when weight is not present.');

        $this->actingAs($me->user)
            ->put(route('patients.treatment_log.update', [$admission->patient, $treatmentLog]), [
                'treated_at' => 'foo',
                'weight' => 'foo',
            ])
            ->assertHasValidationError('treated_at', 'The treated at date is not a valid date.')
            ->assertHasValidationError('weight', 'The weight must be a number.');
    }

    public function test_it_updates_a_treatment_log(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $treatmentLog = TreatmentLog::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.treatment_log.update', [$admission->patient, $treatmentLog]), [
                'treated_at' => '2022-07-01',
                'weight' => '123',
                'weight_unit' => 'gm',
                'comments' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('treatment_logs', [
            'id' => $treatmentLog->id,
            'patient_id' => $admission->patient_id,
            'treated_at' => '2022-07-01 00:00',
            'weight' => '123.00',
            'weight_unit' => 'gm',
            'comments' => 'lorem',
        ]);
    }

    public function test_it_validates_ownership_of_a_treatment_log_before_deleting(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $treatmentLog = TreatmentLog::factory()->create();
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->delete(route('patients.treatment_log.destroy', [$admission->patient, $treatmentLog]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_a_treatment_log(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $treatmentLog = TreatmentLog::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to('manage-treatment-logs');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->delete(route('patients.treatment_log.destroy', [$admission->patient, $treatmentLog]))
            ->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted('treatment_logs', [
            'id' => $treatmentLog->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
