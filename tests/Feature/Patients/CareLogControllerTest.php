<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\CareLog;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class CareLogControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    public function test_un_authenticated_users_cant_store_a_care_log(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.care_log.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_a_care_log(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.care_log.store', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_store_a_care_log(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->post(route('patients.care_log.store', $admission->patient))
            ->assertInvalid([
                'care_at' => 'The care date field is required.',
                'weight' => 'The weight field is required when comments is not present.',
                'comments' => 'The comments field is required when weight is not present.'
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.care_log.store', $admission->patient), [
                'care_at' => 'foo',
                'weight' => 'foo',
                'weight_unit_id' => 123
            ])
            ->assertInvalid([
                'care_at' => 'The care date field must be a valid date.',
                'weight' => 'The weight field must be a number.',
                'weight_unit_id' => 'The selected weight unit is invalid.'
            ]);
    }

    public function test_it_validates_ownership_of_a_patient_before_storing(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->post(route('patients.care_log.store', $patient), [
                'care_at' => '2022-07-01',
                'weight' => '123',
                'weight_unit_id' => $kgWeightId,
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_stores_a_care_log(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.care_log.store', $admission->patient), [
                'care_at' => '2022-07-01 15:43:00',
                'weight' => 123.45,
                'weight_unit_id' => $kgWeightId,
                'comments' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('care_logs', [
            'patient_id' => $admission->patient_id,
            'date_care_at' => '2022-07-01 00:00',
            'time_care_at' => '22:43:00',
            'weight' => 123.45,
            'weight_unit_id' => $kgWeightId,
            'comments' => 'lorem',
        ]);
    }

    public function test_it_fails_validation_when_trying_to_update_a_care_log(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $careLog = CareLog::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->put(route('patients.care_log.update', [$admission->patient, $careLog]))
            ->assertInvalid([
                'care_at' => 'The care date field is required.',
                'weight' => 'The weight field is required when comments is not present.',
                'comments' => 'The comments field is required when weight is not present.'
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.care_log.update', [$admission->patient, $careLog]), [
                'care_at' => 'foo',
                'weight' => 'foo',
                'weight_unit_id' => 123
            ])
            ->assertInvalid([
                'care_at' => 'The care date field must be a valid date.',
                'weight' => 'The weight field must be a number.',
                'weight_unit_id' => 'The selected weight unit is invalid.'
            ]);
    }

    public function test_it_validates_ownership_of_a_care_log_before_updating(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $careLog = CareLog::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->put(route('patients.care_log.update', [$patient, $careLog]), [
                'care_at' => '2022-07-01',
                'weight' => '123',
                'weight_unit_id' => $kgWeightId,
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_updates_a_care_log(): void
    {
        [$kgWeightId] = $this->weightUnits();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $careLog = CareLog::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.care_log.update', [$admission->patient, $careLog]), [
                'care_at' => '2022-07-01 15:43:00',
                'weight' => '123',
                'weight_unit_id' => $kgWeightId,
                'comments' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('care_logs', [
            'id' => $careLog->id,
            'patient_id' => $admission->patient_id,
            'date_care_at' => '2022-07-01',
            'time_care_at' => '22:43:00',
            'weight' => '123.00',
            'weight_unit_id' => $kgWeightId,
            'comments' => 'lorem',
        ]);
    }

    public function test_it_validates_ownership_of_a_care_log_before_deleting(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $careLog = CareLog::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->delete(route('patients.care_log.destroy', [$admission->patient, $careLog]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_a_care_log(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $careLog = CareLog::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_CARE_LOGS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->delete(route('patients.care_log.destroy', [$admission->patient, $careLog]))
            ->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted('care_logs', [
            'id' => $careLog->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
