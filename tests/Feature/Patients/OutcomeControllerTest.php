<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class OutcomeControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    public function test_un_authenticated_users_cant_update_the_outcome(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.outcome.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_outcome(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.outcome.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_outcome(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_outcome(): void
    {
        $this->pendingDispositionId();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2022-07-02', 'time_admitted_at' => '13:22:00']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient))
            ->assertInvalid(['disposition_id' => 'The disposition field is required.']);

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition_id' => 'foo',
            ])
            ->assertInvalid([
                'disposition_id' => 'The selected disposition is invalid.',
                'dispositioned_at' => 'The disposition date field is required unless disposition is in Pending.'
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition_id' => 'foo',
                'dispositioned_at' => 'foo',
            ])
            ->assertInvalid(['dispositioned_at' => 'The disposition date is not a valid date.']);

        // 2022-07-01 00:00:00.0
        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition_id' => 'foo',
                'dispositioned_at' => '2022-07-01',
            ])
            ->assertInvalid(['dispositioned_at' => 'The disposition date must be a date after or equal to Jul 2, 2022.']);

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition_id' => 'foo',
                'release_type_id' => 'foo',
                'transfer_type_id' => 'foo',
                'is_carcass_saved' => 'foo',
            ])
            ->assertInvalid([
                'release_type_id' => 'The selected release type is invalid.',
                'transfer_type_id' => 'The selected transfer type is invalid.',
                'is_carcass_saved' => 'The carcass saved field must be true or false.'
            ]);
    }

    public function test_it_updates_the_outcome(): void
    {
        $pendingDispositionId = $this->pendingDispositionId();
        $releaseTypeId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_RELEASE_TYPES])->id;
        $transferTypeId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_TRANSFER_TYPES])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2022-07-01']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition_id' => $pendingDispositionId,
                'transfer_type_id' => $transferTypeId,
                'release_type_id' => $releaseTypeId,
                'dispositioned_at' => '2022-07-22',
                'disposition_address' => '123 Main St.',
                'disposition_city' => 'Any Town',
                'disposition_subdivision' => 'CA',
                'disposition_postal_code' => '98765',
                'reason_for_disposition' => 'Look good',
                'dispositioned_by' => 'Jim',
                'is_carcass_saved' => '0',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'disposition_id' => $pendingDispositionId,
            'dispositioned_at' => '2022-07-22',
            'release_type_id' => $releaseTypeId,
            'transfer_type_id' => $transferTypeId,
            'disposition_address' => '123 Main St.',
            'disposition_city' => 'Any Town',
            'disposition_subdivision' => 'CA',
            'disposition_postal_code' => '98765',
            'reason_for_disposition' => 'Look good',
            'dispositioned_by' => 'Jim',
            'is_carcass_saved' => 0,
        ]);
    }
}
