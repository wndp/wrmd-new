<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class DiagnosisControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_the_diagnosis(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.diagnosis.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_diagnosis(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.diagnosis.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_diagnosis(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->json('put', route('patients.diagnosis.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_updates_the_diagnosis(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-patient-care');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->json('put', route('patients.diagnosis.update', $admission->patient), [
                'diagnosis' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'diagnosis' => 'lorem',
        ]);
    }
}
