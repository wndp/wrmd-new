<?php

namespace Tests\Feature\Patients\BandingAndMorphometrics;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Models\AttributeOption;
use App\Models\Banding;
use App\Models\Patient;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class RecaptureControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    private function createAttributeOptions()
    {
        return [
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_RECAPTURE_DISPOSITION_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_PRESENT_CONDITION_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_HOW_PRESENT_CONDITION_CODES])->id,
        ];
    }

    public function test_un_authenticated_users_cant_update_recapture(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.banding-morphometrics.recapture.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_recapture(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.banding-morphometrics.recapture.update', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_update_the_recapture(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-28', 'time_admitted_at' => '08:30']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.recapture.update', $admission->patient))
            ->assertInvalid(['recaptured_at' => 'The recapture date field is required.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.recapture.update', $admission->patient), [
                'recaptured_at' => 'foo',
            ])
            ->assertInvalid(['recaptured_at' => 'The recapture date is not a valid date.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.recapture.update', $admission->patient), [
                'recaptured_at' => '2024-12-25',
            ])
            ->assertInvalid(['recaptured_at' => 'The recapture date must be a date after or equal to 2024-12-28']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.recapture.update', $admission->patient), [
                'recapture_disposition_id' => 123,
                'present_condition_id' => 123,
                'how_present_condition_id' => 123,
            ])
            ->assertInvalid([
                'recapture_disposition_id' => 'The selected recapture disposition is invalid.',
                'present_condition_id' => 'The selected present condition is invalid.',
                'how_present_condition_id' => 'The selected how present condition is invalid.',
            ]);
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_recapture(): void
    {
        [
            $recaptureDispositionCodesId,
            $presentConditionCodesId,
            $howPresentConditionCodesId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.recapture.update', $patient), [
                'recapture_disposition_id' => $recaptureDispositionCodesId,
                'present_condition_id' => $presentConditionCodesId,
                'how_present_condition_id' => $howPresentConditionCodesId,
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_recapture(): void
    {
        [
            $recaptureDispositionCodesId,
            $presentConditionCodesId,
            $howPresentConditionCodesId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2023-06-01']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.recapture.update', $admission->patient), [
                'recaptured_at' => '2023-06-06',
                'recapture_disposition_id' => $recaptureDispositionCodesId,
                'present_condition_id' => $presentConditionCodesId,
                'how_present_condition_id' => $howPresentConditionCodesId,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'patient_id' => $admission->patient_id,
            'recaptured_at' => '2023-06-06',
            'recapture_disposition_id' => $recaptureDispositionCodesId,
            'present_condition_id' => $presentConditionCodesId,
            'how_present_condition_id' => $howPresentConditionCodesId,
        ]);
    }

    public function test_it_updates_an_existing_recapture(): void
    {
        [
            $recaptureDispositionCodesId,
            $presentConditionCodesId,
            $howPresentConditionCodesId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2023-06-01']);
        $banding = Banding::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.recapture.update', $admission->patient), [
                'recaptured_at' => '2023-06-06',
                'recapture_disposition_id' => $recaptureDispositionCodesId,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'id' => $banding->id,
            'recaptured_at' => '2023-06-06',
            'recapture_disposition_id' => $recaptureDispositionCodesId,
        ]);
    }
}
