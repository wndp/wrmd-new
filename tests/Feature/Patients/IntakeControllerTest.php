<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class IntakeControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_the_intake(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.intake.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_intake(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.intake.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_intake(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_intake(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $admission->patient), [
                'found_at' => 'foo',
            ])
            ->assertHasValidationError('found_at', 'The found at is not a valid date.');
    }

    public function test_it_updates_the_intake(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.intake.update', $admission->patient), [
                'transported_by' => 'Jim',
                'admitted_by' => 'Pam',
                'found_at' => '2020-07-02',
                'address_found' => '123 Main St',
                'city_found' => 'Any Town',
                'subdivision_found' => 'CA',
                'reasons_for_admission' => 'Hit by car',
                'care_by_rescuer' => 'Water',
                'notes_about_rescue' => 'Nice and easy',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'transported_by' => 'Jim',
            'admitted_by' => 'Pam',
            'found_at' => '2020-07-02',
            'address_found' => '123 Main St',
            'city_found' => 'Any Town',
            'subdivision_found' => 'CA',
            'reasons_for_admission' => 'Hit by car',
            'care_by_rescuer' => 'Water',
            'notes_about_rescue' => 'Nice and easy',
        ]);
    }
}
