<?php

namespace Tests\Feature\Patients\Research;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Research\Models\Banding;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class AuxiliaryMarkerControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_auxiliary_markers(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.research.auxiliary_marker.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_auxiliary_markers(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.research.auxiliary_marker.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_auxiliary_markers(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.auxiliary_marker.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_auxiliary_markers(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.auxiliary_marker.update', $admission->patient))
            ->assertHasValidationError('auxiliary_marker', 'The auxiliary marker field is required.');
    }

    public function test_it_saves_a_new_morphometrics(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.auxiliary_marker.update', $admission->patient), [
                'auxiliary_marker' => 'abc123',
                'auxiliary_marker_color' => 'red',
                'auxiliary_side_of_bird' => 'Right',
                'auxiliary_marker_type' => 'metal',
                'auxiliary_marker_code_color' => 'blue',
                'auxiliary_placement_on_leg' => 'Above the joint',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'patient_id' => $admission->patient_id,
            'auxiliary_marker' => 'abc123',
            'auxiliary_marker_color' => 'red',
            'auxiliary_side_of_bird' => 'Right',
            'auxiliary_marker_type' => 'metal',
            'auxiliary_marker_code_color' => 'blue',
            'auxiliary_placement_on_leg' => 'Above the joint',
        ]);
    }

    public function test_it_updates_an_existing_auxiliary_markers(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $banding = Banding::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.auxiliary_marker.update', $admission->patient), [
                'auxiliary_marker' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'id' => $banding->id,
            'auxiliary_marker' => 'lorem',
        ]);
    }
}
