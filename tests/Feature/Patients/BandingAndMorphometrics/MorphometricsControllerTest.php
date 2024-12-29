<?php

namespace Tests\Feature\Patients\BandingAndMorphometrics;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Morphometric;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class MorphometricsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_morphometrics(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.banding-morphometrics.morphometrics.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_morphometrics(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.banding-morphometrics.morphometrics.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_morphometrics(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.morphometrics.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_morphometrics(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-28', 'time_admitted_at' => '08:30']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.morphometrics.update', $admission->patient))
            ->assertInvalid(['measured_at' => 'The date measured field is required.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.morphometrics.update', $admission->patient), [
                'measured_at' => 'foo',
            ])
            ->assertInvalid(['measured_at' => 'The date measured is not a valid date.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.morphometrics.update', $admission->patient), [
                'measured_at' => '2024-12-25',
            ])
            ->assertInvalid(['measured_at' => 'The date measured must be a date after or equal to 2024-12-28']);
    }

    public function test_it_saves_a_new_morphometrics(): void
    {
        $samples = AttributeOption::factory()->count(3)->create(['name' => AttributeOptionName::BANDING_SAMPLES_COLLECTED]);

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2023-06-01']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.morphometrics.update', $admission->patient), [
                'measured_at' => '2023-06-06',
                'bill_length' => '11',
                'bill_width' => '22',
                'bill_depth' => '33',
                'head_bill_length' => '44',
                'culmen' => '55',
                'exposed_culmen' => '66',
                'wing_chord' => '77',
                'flat_wing' => '88',
                'tarsus_length' => '99',
                'middle_toe_length' => '111',
                'toe_pad_length' => '222',
                'hallux_length' => '333',
                'tail_length' => '444',
                'weight' => '555',
                'samples_collected' => $samples->pluck('id')->toArray(),
                'remarks' => 'lorem ipsum',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('morphometrics', [
            'patient_id' => $admission->patient_id,
            'measured_at' => '2023-06-06',
            'bill_length' => '11',
            'bill_width' => '22',
            'bill_depth' => '33',
            'head_bill_length' => '44',
            'culmen' => '55',
            'exposed_culmen' => '66',
            'wing_chord' => '77',
            'flat_wing' => '88',
            'tarsus_length' => '99',
            'middle_toe_length' => '111',
            'toe_pad_length' => '222',
            'hallux_length' => '333',
            'tail_length' => '444',
            'weight' => '555',
            'samples_collected' => json_encode($samples->pluck('id')->toArray()),
            'remarks' => 'lorem ipsum',
        ]);
    }

    public function test_it_updates_an_existing_morphometrics(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2023-06-01']);
        $morphometric = Morphometric::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.morphometrics.update', $admission->patient), [
                'measured_at' => '2023-06-06',
                'remarks' => 'lorem ipsum',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('morphometrics', [
            'id' => $morphometric->id,
            'measured_at' => '2023-06-06',
            'remarks' => 'lorem ipsum',
        ]);
    }
}
