<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class OutcomeControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_the_outcome(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.outcome.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_outcome(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.outcome.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_outcome(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_outcome(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2022-07-02 13:22:00']);
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient))
            ->assertHasValidationError('disposition', 'The disposition field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition' => 'foo',
            ])
            ->assertHasValidationError('disposition', 'The selected disposition is invalid.')
            ->assertHasValidationError('dispositioned_at', 'The disposition date field is required unless disposition is in Pending.');

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition' => 'foo',
                'dispositioned_at' => 'foo',
            ])
            ->assertHasValidationError('dispositioned_at', 'The disposition date is not a valid date.');

        // 2022-07-01 00:00:00.0
        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition' => 'foo',
                'dispositioned_at' => '2022-07-01',
            ])
            ->assertHasValidationError('dispositioned_at', 'The disposition date must be a date after or equal to Jul 2, 2022.');

        $this->actingAs($me->user)
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition' => 'foo',
                'release_type' => 'foo',
                'transfer_type' => 'foo',
                'carcass_saved' => 'foo',
            ])
            ->assertHasValidationError('release_type', 'The selected release type is invalid.')
            ->assertHasValidationError('transfer_type', 'The selected transfer type is invalid.')
            ->assertHasValidationError('carcass_saved', 'The carcass saved field must be true or false.');
    }

    public function test_it_updates_the_outcome(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2022-07-01']);
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.outcome.update', $admission->patient), [
                'disposition' => 'Released',
                'transfer_type' => 'Released',
                'release_type' => 'Soft',
                'dispositioned_at' => '2022-07-22',
                'disposition_address' => '123 Main St.',
                'disposition_city' => 'Any Town',
                'disposition_subdivision' => 'CA',
                'disposition_postal_code' => '98765',
                'reason_for_disposition' => 'Look good',
                'dispositioned_by' => 'Jim',
                'carcass_saved' => '0',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'disposition' => 'Released',
            'dispositioned_at' => '2022-07-22',
            'release_type' => 'Soft',
            'transfer_type' => 'Released',
            'disposition_address' => '123 Main St.',
            'disposition_city' => 'Any Town',
            'disposition_subdivision' => 'CA',
            'disposition_postal_code' => '98765',
            'reason_for_disposition' => 'Look good',
            'dispositioned_by' => 'Jim',
            'carcass_saved' => 0,
        ]);
    }
}
