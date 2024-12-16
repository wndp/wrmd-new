<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Veterinarian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class PrescriptionControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantStoreAPrescription(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.prescription.store', $patient))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantStoreAPrescription(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.prescription.store', $patient))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfAPatientBeforeStoring(): void
    {
        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->post(route('patients.prescription.store', $patient), [
                'drug' => 'Foo',
                'frequency_id' => $frequencyIs1DailyId,
                'rx_started_at' => '2022-09-05',
            ])
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itFailsValidationWhenTryingToStoreAPrescription(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->post(route('patients.prescription.store', $admission->patient))
            ->assertInvalid([
                'rx_started_at' => 'The start date field is required.',
                'frequency_id' => 'The frequency id field is required.',
                'drug' => 'The drug field is required.',
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.prescription.store', $admission->patient), [
                'rx_started_at' => 'foo',
            ])
            ->assertInvalid(['rx_started_at' => 'The start date is not a valid date.']);

        $this->actingAs($me->user)
            ->post(route('patients.prescription.store', $admission->patient), [
                'rx_started_at' => '2022-06-30',
                'rx_ended_at' => '2022-06-28',
            ])
            ->assertInvalid(['rx_ended_at' => 'The end date must be a date after or equal to the start date.']);
    }

    #[Test]
    public function itStoresAPrescription(): void
    {
        $concentrationUnitIsMgPerMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML
        )->attribute_option_id;

        $dosageUnitIsMgPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG
        )->attribute_option_id;

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        )->attribute_option_id;

        $doseUnitIsCapId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_CAP
        )->attribute_option_id;

        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $routeIsOralId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_ROUTES,
            AttributeOptionUiBehavior::DAILY_TASK_ROUTE_IS_ORAL
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.prescription.store', $admission->patient), [
                'veterinarian_id' => $veterinarian->id,
                'drug' => 'Foo',
                'concentration' => 11,
                'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
                'dosage' => 22,
                'dosage_unit_id' => $dosageUnitIsMgPerKgId,
                'loading_dose' => 33,
                'loading_dose_unit_id' => $doseUnitIsMlId,
                'dose' => 44,
                'dose_unit_id' => $doseUnitIsCapId,
                'frequency_id' => $frequencyIs1DailyId,
                'route_id' => $routeIsOralId,
                'rx_started_at' => '2022-09-05',
                'rx_ended_at' => '2022-09-06',
                'is_controlled_substance' => false,
                'instructions' => 'Bar',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('prescriptions', [
            'patient_id' => $admission->patient_id,
            'veterinarian_id' => $veterinarian->id,
            'drug' => 'Foo',
            'concentration' => 11,
            'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
            'dosage' => 22,
            'dosage_unit_id' => $dosageUnitIsMgPerKgId,
            'loading_dose' => 33,
            'loading_dose_unit_id' => $doseUnitIsMlId,
            'dose' => 44,
            'dose_unit_id' => $doseUnitIsCapId,
            'frequency_id' => $frequencyIs1DailyId,
            'route_id' => $routeIsOralId,
            'rx_started_at' => '2022-09-05',
            'rx_ended_at' => '2022-09-06',
            'is_controlled_substance' => false,
            'instructions' => 'Bar',
        ]);
    }

    #[Test]
    public function itValidatesOwnershipOfAPrescriptionBeforeUpdating(): void
    {
        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $prescription = Prescription::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->put(route('patients.prescription.update', [$patient, $prescription]), [
                'drug' => 'Foo',
                'frequency_id' => $frequencyIs1DailyId,
                'rx_started_at' => '2022-09-05',
            ])
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itFailsValidationWhenTryingToUpdateAPrescription(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $prescription = Prescription::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->put(route('patients.prescription.update', [$admission->patient, $prescription]))
            ->assertInvalid([
                'rx_started_at' => 'The start date field is required.',
                'frequency_id' => 'The frequency id field is required.',
                'drug' => 'The drug field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.prescription.update', [$admission->patient, $prescription]), [
                'rx_started_at' => 'foo',
            ])
            ->assertInvalid(['rx_started_at' => 'The start date is not a valid date.']);

        $this->actingAs($me->user)
            ->put(route('patients.prescription.update', [$admission->patient, $prescription]), [
                'rx_started_at' => '2022-06-30',
                'rx_ended_at' => '2022-06-28',
            ])
            ->assertInvalid(['rx_ended_at' => 'The end date must be a date after or equal to the start date.']);
    }

    #[Test]
    public function itUpdatesAPrescription(): void
    {
        $concentrationUnitIsMgPerMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_CONCENTRATION_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_CONCENTRATION_UNIT_IS_MG_PER_ML
        )->attribute_option_id;

        $dosageUnitIsMgPerKgId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSAGE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSAGE_UNIT_IS_MG_PER_KG
        )->attribute_option_id;

        $doseUnitIsMlId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_ML
        )->attribute_option_id;

        $doseUnitIsCapId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_DOSE_UNITS,
            AttributeOptionUiBehavior::DAILY_TASK_DOSE_UNIT_IS_CAP
        )->attribute_option_id;

        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $routeIsOralId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_ROUTES,
            AttributeOptionUiBehavior::DAILY_TASK_ROUTE_IS_ORAL
        )->attribute_option_id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $veterinarian = Veterinarian::factory()->create(['team_id' => $me->team->id]);
        $prescription = Prescription::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.prescription.update', [$admission->patient, $prescription]), [
                'veterinarian_id' => $veterinarian->id,
                'drug' => 'Foo',
                'concentration' => 11,
                'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
                'dosage' => 22,
                'dosage_unit_id' => $dosageUnitIsMgPerKgId,
                'loading_dose' => 33,
                'loading_dose_unit_id' => $doseUnitIsMlId,
                'dose' => 44,
                'dose_unit_id' => $doseUnitIsCapId,
                'frequency_id' => $frequencyIs1DailyId,
                'route_id' => $routeIsOralId,
                'rx_started_at' => '2022-09-05',
                'rx_ended_at' => '2022-09-06',
                'is_controlled_substance' => false,
                'instructions' => 'Bar',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('prescriptions', [
            'id' => $prescription->id,
            'patient_id' => $admission->patient_id,
            'veterinarian_id' => $veterinarian->id,
            'drug' => 'Foo',
            'concentration' => 11,
            'concentration_unit_id' => $concentrationUnitIsMgPerMlId,
            'dosage' => 22,
            'dosage_unit_id' => $dosageUnitIsMgPerKgId,
            'loading_dose' => 33,
            'loading_dose_unit_id' => $doseUnitIsMlId,
            'dose' => 44,
            'dose_unit_id' => $doseUnitIsCapId,
            'frequency_id' => $frequencyIs1DailyId,
            'route_id' => $routeIsOralId,
            'rx_started_at' => '2022-09-05',
            'rx_ended_at' => '2022-09-06',
            'is_controlled_substance' => false,
            'instructions' => 'Bar',
        ]);
    }
}
