<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Necropsy;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class NecropsyMorphometricsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    private function createAttributeOptions()
    {
        return [
            AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_WEIGHT_UNITS])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_AVES_AGE_UNITS])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_SEXES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_BODY_CONDITIONS])->id,
        ];
    }

    public function test_un_authenticated_users_cant_update_necropsy_morphometrics(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.necropsy.morphometrics.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_necropsy_morphometrics(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.necropsy.morphometrics.update', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_update_the_necropsy_morphometrics(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.morphometrics.update', $admission->patient), [
                'sex_id' => 123,
                'weight' => 'foo',
                'weight_unit_id' => 123,
                'body_condition_id' => 123,
                'age' => 'foo',
                'age_unit_id' => 123,
                'wing' => 'foo',
                'tarsus' => 'foo',
                'culmen' => 'foo',
                'exposed_culmen' => 'foo',
                'bill_depth' => 'foo',
            ])
            ->assertInvalid([
                'sex_id' => 'The selected sex is invalid.',
                'weight' => 'The weight field must be a number',
                'weight_unit_id' => 'The selected weight unit is invalid.',
                'body_condition_id' => 'The selected body condition is invalid.',
                'age' => 'The age field must be a number.',
                'age_unit_id' => 'The selected age unit is invalid.',
                'wing' => 'The wing field must be a number.',
                'tarsus' => 'The tarsus field must be a number.',
                'culmen' => 'The culmen field must be a number.',
                'exposed_culmen' => 'The exposed culmen field must be a number.',
                'bill_depth' => 'The bill depth field must be a number.',
            ]);
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy_morphometrics(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.morphometrics.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_necropsy_morphometrics(): void
    {
        [
            $weightUnitId,
            $avesAgeUnitId,
            $sexeId,
            $bodyConditionId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.morphometrics.update', $admission->patient), [
                'weight' => '555',
                'weight_unit_id' => $weightUnitId,
                'age' => '1',
                'age_unit_id' => $avesAgeUnitId,
                'sex_id' => $sexeId,
                'body_condition_id' => $bodyConditionId,
                'wing' => '22',
                'tarsus' => '33',
                'culmen' => '44',
                'exposed_culmen' => '55',
                'bill_depth' => '66',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'weight' => '555',
            'weight_unit_id' => $weightUnitId,
            'age' => '1',
            'age_unit_id' => $avesAgeUnitId,
            'sex_id' => $sexeId,
            'body_condition_id' => $bodyConditionId,
            'wing' => '22',
            'tarsus' => '33',
            'culmen' => '44',
            'exposed_culmen' => '55',
            'bill_depth' => '66',
        ]);
    }

    public function test_it_updates_an_existing_necropsy_morphometrics(): void
    {
        [
            $weightUnitId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.morphometrics.update', $admission->patient), [
                'weight' => '555',
                'weight_unit_id' => $weightUnitId,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'weight' => '555',
            'weight_unit_id' => $weightUnitId,
        ]);
    }
}
