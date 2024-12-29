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

final class BandingControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    private function createAttributeOptions()
    {
        return [
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_AGE_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_HOW_AGED_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_SEX_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_HOW_SEXED_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_STATUS_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_ADDITIONAL_INFORMATION_STATUS_CODES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_BAND_SIZES])->id,
            AttributeOption::factory()->create(['name' => AttributeOptionName::BANDING_DISPOSITION_CODES])->id,
        ];
    }

    public function test_un_authenticated_users_cant_update_banding(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.banding-morphometrics.banding.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_banding(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.banding-morphometrics.banding.update', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_update_the_banding(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-28', 'time_admitted_at' => '08:30']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient))
            ->assertInvalid(['band_number' => 'The band number field is required.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient))
            ->assertInvalid(['banded_at' => 'The banding date field is required.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient), [
                'banded_at' => 'foo',
            ])
            ->assertInvalid(['banded_at' => 'The banding date is not a valid date.']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient), [
                'banded_at' => '2024-12-25',
            ])
            ->assertInvalid(['banded_at' => 'The banding date must be a date after or equal to 2024-12-28']);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient), [
                'age_code_id' => 123,
                'how_aged_id' => 123,
                'sex_code_id' => 123,
                'how_sexed_id' => 123,
                'status_code_id' => 123,
                'additional_status_code_id' => 123,
                'band_size_id' => 123,
                'band_disposition_id' => 123,
            ])
            ->assertInvalid([
                'age_code_id' => 'The selected age code is invalid.',
                'how_aged_id' => 'The selected how age code is invalid.',
                'sex_code_id' => 'The selected sex code is invalid.',
                'how_sexed_id' => 'The selected hoe sexed code is invalid.',
                'status_code_id' => 'The selected status code is invalid.',
                'additional_status_code_id' => 'The selected additional status code is invalid.',
                'band_size_id' => 'The selected band size is invalid.',
                'band_disposition_id' => 'The selected band disposition is invalid.',
            ]);
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_banding(): void
    {
        [
            $ageCodeId,
            $howAgedCodeId,
            $sexCodeId,
            $howSexedCodeId,
            $statusCodeId,
            $additionalInformationStatusCodeId,
            $bandSizeId,
            $dispositionCodeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->put(route('patients.banding-morphometrics.banding.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_banding(): void
    {
        [
            $ageCodeId,
            $howAgedCodeId,
            $sexCodeId,
            $howSexedCodeId,
            $statusCodeId,
            $additionalInformationStatusCodeId,
            $bandSizeId,
            $dispositionCodeId,
        ] = $this->createAttributeOptions();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2023-06-01']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient), [
                'band_number' => 'abc123',
                'banded_at' => '2023-06-06',
                'age_code_id' => $ageCodeId,
                'how_aged_id' => $howAgedCodeId,
                'sex_code_id' => $sexCodeId,
                'how_sexed_id' => $howSexedCodeId,
                'status_code_id' => $statusCodeId,
                'additional_status_code_id' => $additionalInformationStatusCodeId,
                'band_size_id' => $bandSizeId,
                'master_bander_number' => 'SOS',
                'banded_by' => 'John Doe',
                'location_number' => 'LB123',
                'band_disposition_id' => $dispositionCodeId,
                'remarks' => 'test',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'patient_id' => $admission->patient_id,
            'band_number' => 'abc123',
            'banded_at' => '2023-06-06',
            'age_code_id' => $ageCodeId,
            'how_aged_id' => $howAgedCodeId,
            'sex_code_id' => $sexCodeId,
            'how_sexed_id' => $howSexedCodeId,
            'status_code_id' => $statusCodeId,
            'additional_status_code_id' => $additionalInformationStatusCodeId,
            'band_size_id' => $bandSizeId,
            'master_bander_number' => 'SOS',
            'banded_by' => 'John Doe',
            'location_number' => 'LB123',
            'band_disposition_id' => $dispositionCodeId,
            'remarks' => 'test',
        ]);
    }

    public function test_it_updates_an_existing_banding(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2023-06-01']);
        $banding = Banding::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_BANDING_AND_MORPHOMETRICS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.banding-morphometrics.banding.update', $admission->patient), [
                'band_number' => 'abc123',
                'banded_at' => '2023-06-06',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'id' => $banding->id,
            'band_number' => 'abc123',
            'banded_at' => '2023-06-06',
        ]);
    }
}
