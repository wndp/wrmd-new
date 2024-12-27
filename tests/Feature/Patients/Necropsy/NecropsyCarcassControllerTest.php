<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Necropsy\Necropsy;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class NecropsyCarcassControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_carcass(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.necropsy.carcass.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_carcass(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.necropsy.carcass.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_carcass(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.carcass.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_carcass(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.carcass.update', $admission->patient), [
                'is_previously_frozen' => 'test',
            ])
            ->assertHasValidationError('is_previously_frozen', 'The is previously frozen field must be true or false.');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.carcass.update', $admission->patient), [
                'is_scavenged' => 'test',
            ])
            ->assertHasValidationError('is_scavenged', 'The is scavenged field must be true or false.');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.carcass.update', $admission->patient), [
                'is_discarded' => 'test',
            ])
            ->assertHasValidationError('is_discarded', 'The is discarded field must be true or false.');
    }

    public function test_it_saves_a_new_carcass(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.carcass.update', $admission->patient), [
                'carcass_condition' => 'Fresh',
                'is_previously_frozen' => 0,
                'is_scavenged' => 1,
                'is_discarded' => 0,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'carcass_condition' => 'Fresh',
            'is_previously_frozen' => 0,
            'is_scavenged' => 1,
            'is_discarded' => 0,
        ]);
    }

    public function test_it_updates_an_existing_carcass(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.carcass.update', $admission->patient), [
                'carcass_condition' => 'Fresh',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'carcass_condition' => 'Fresh',
        ]);
    }
}
