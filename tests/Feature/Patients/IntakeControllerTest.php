<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Enums\SettingKey;
use App\Models\Patient;
use App\ValueObjects\SingleStorePoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class IntakeControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_the_intake(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.intake.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_intake(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.intake.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_intake(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $patient), [
                'admitted_by' => 'Pam',
                'found_at' => '2020-07-02',
                'address_found' => '123 Main St',
                'city_found' => 'Any Town',
                'subdivision_found' => 'CA',
                'reason_for_admission' => 'Hit by car',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_intake(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $admission->patient))
            ->assertInvalid([
                'admitted_by' => 'The admitted by field is required.',
                'found_at' => 'The found at field is required.',
                'address_found' => 'The address found field is required.',
                'city_found' => 'The city found field is required.',
                'subdivision_found' => 'The subdivision found field is required.',
                'reason_for_admission' => 'The reason for admission field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $admission->patient), [
                'found_at' => 'foo',
            ])
            ->assertInvalid(['found_at' => 'The found at field must be a valid date']);
    }

    public function test_it_validates_the_coordinates_if_the_account_displays_the_fields()
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->setSetting($me->team, SettingKey::SHOW_GEOLOCATION_FIELDS, true);

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $admission->patient), [
                'latitude_found' => 'foo',
                'longitude_found' => 'bar',
            ])
            ->assertInvalid([
                'latitude_found' => 'The latitude found field must be a number.',
                'longitude_found' => 'The longitude found field must be a number.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.intake.update', $admission->patient), [
                'latitude_found' => '100',
                'longitude_found' => '200',
            ])
            ->assertInvalid([
                'latitude_found' => 'The latitude found field must be between -90 and 90.',
                'longitude_found' => 'The longitude found field must be between -180 and 180.',
            ]);
    }

    public function test_it_updates_the_intake(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_CARE->value);

        $this->setSetting($me->team, SettingKey::SHOW_GEOLOCATION_FIELDS, true);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.intake.update', $admission->patient), [
                'transported_by' => 'Jim',
                'admitted_by' => 'Pam',
                'found_at' => '2020-07-02',
                'address_found' => '123 Main St',
                'city_found' => 'Any Town',
                'subdivision_found' => 'CA',
                'latitude_found' => '32.99887766',
                'longitude_found' => '-121.11223344',
                'reason_for_admission' => 'Hit by car',
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
            'coordinates_found' => (new SingleStorePoint('32.99887766', '-121.11223344'))->toWkt(),
            'reason_for_admission' => 'Hit by car',
            'care_by_rescuer' => 'Water',
            'notes_about_rescue' => 'Nice and easy',
        ]);
    }
}
