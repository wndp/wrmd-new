<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class PatientMetaControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_the_meta(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.meta.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_meta(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.meta.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_meta(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-meta');

        $this->actingAs($me->user)
            ->put(route('patients.meta.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_meta(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-meta');

        $this->actingAs($me->user)
            ->put(route('patients.meta.update', $admission->patient))
            ->assertHasValidationError('is_frozen', 'The is frozen field is required.')
            ->assertHasValidationError('is_voided', 'The is voided field is required.')
            ->assertHasValidationError('criminal_activity', 'The criminal activity field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.meta.update', $admission->patient), [
                'is_frozen' => 'foo',
                'is_voided' => 'foo',
                'criminal_activity' => 'foo',
                'keywords' => 1,
            ])
            ->assertHasValidationError('is_frozen', 'The is frozen field must be true or false.')
            ->assertHasValidationError('is_voided', 'The is voided field must be true or false.')
            ->assertHasValidationError('criminal_activity', 'The criminal activity field must be true or false.')
            ->assertHasValidationError('keywords', 'The keywords must be a string.');
    }

    public function test_it_updates_the_meta(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-meta');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.meta.update', $admission->patient), [
                'is_frozen' => 0,
                'is_voided' => 1,
                'is_resident' => 1,
                'criminal_activity' => 0,
                'keywords' => 'test',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'is_frozen' => false,
            'is_voided' => true,
            'is_resident' => true,
            'criminal_activity' => false,
            'keywords' => 'test',
        ]);
    }
}
