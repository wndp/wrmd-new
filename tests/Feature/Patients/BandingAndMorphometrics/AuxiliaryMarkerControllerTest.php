<?php

namespace Tests\Feature\Patients\BandingAndMorphometrics;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Banding;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class AuxiliaryMarkerControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    private function createAttributeOptions()
    {
        return [
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_AUXILLARY_COLOR_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_AUXILLARY_SIDE_OF_BIRD])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_AUXILLARY_MARKER_TYPE_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_AUXILLARY_CODE_COLOR])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_PLACEMENT_ON_LEG])->id,
        ];
    }

    public function test_un_authenticated_users_cant_update_auxiliary_markers(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.banding-morphometrics.auxiliary_marker.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_auxiliary_markers(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.banding-morphometrics.auxiliary_marker.update', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_update_the_auxiliary_markers(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.auxiliary_marker.update', $admission->patient), [
                'auxiliary_marker_color_id' => 123,
                'auxiliary_side_of_bird_id' => 123,
                'auxiliary_marker_type_id' => 123,
                'auxiliary_marker_code_color_id' => 123,
                'auxiliary_placement_on_leg_id' => 123,
            ])
            ->assertInvalid([
                'auxiliary_marker' => 'The auxiliary marker field is required.',
                'auxiliary_marker_color_id' => 'The selected auxiliary marker color is invalid.',
                'auxiliary_side_of_bird_id' => 'The selected auxiliary side of bird is invalid.',
                'auxiliary_marker_type_id' => 'The selected auxiliary marker type is invalid.',
                'auxiliary_marker_code_color_id' => 'The selected auxiliary marker code color is invalid.',
                'auxiliary_placement_on_leg_id' => 'The selected auxiliary placement on leg is invalid.',
            ]);
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_auxiliary_markers(): void
    {
        [
            $auxillaryColorCodeId,
            $auxillarySideOfBirdId,
            $auxillaryMarkerTypeCodesId,
            $auxillaryCodeColorId,
            $placementOnLegId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.auxiliary_marker.update', $patient), [
                'auxiliary_marker' => 'abc123',
                'auxiliary_marker_color_id' => $auxillaryColorCodeId,
                'auxiliary_side_of_bird_id' => $auxillarySideOfBirdId,
                'auxiliary_marker_type_id' => $auxillaryMarkerTypeCodesId,
                'auxiliary_marker_code_color_id' => $auxillaryCodeColorId,
                'auxiliary_placement_on_leg_id' => $placementOnLegId,
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_morphometrics(): void
    {
        [
            $auxillaryColorCodeId,
            $auxillarySideOfBirdId,
            $auxillaryMarkerTypeCodesId,
            $auxillaryCodeColorId,
            $placementOnLegId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.auxiliary_marker.update', $admission->patient), [
                'auxiliary_marker' => 'abc123',
                'auxiliary_marker_color_id' => $auxillaryColorCodeId,
                'auxiliary_side_of_bird_id' => $auxillarySideOfBirdId,
                'auxiliary_marker_type_id' => $auxillaryMarkerTypeCodesId,
                'auxiliary_marker_code_color_id' => $auxillaryCodeColorId,
                'auxiliary_placement_on_leg_id' => $placementOnLegId,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'patient_id' => $admission->patient_id,
            'auxiliary_marker' => 'abc123',
            'auxiliary_marker_color_id' => $auxillaryColorCodeId,
            'auxiliary_side_of_bird_id' => $auxillarySideOfBirdId,
            'auxiliary_marker_type_id' => $auxillaryMarkerTypeCodesId,
            'auxiliary_marker_code_color_id' => $auxillaryCodeColorId,
            'auxiliary_placement_on_leg_id' => $placementOnLegId,
        ]);
    }

    public function test_it_updates_an_existing_auxiliary_markers(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $banding = Banding::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.auxiliary_marker.update', $admission->patient), [
                'auxiliary_marker' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'id' => $banding->id,
            'auxiliary_marker' => 'lorem',
        ]);
    }
}
