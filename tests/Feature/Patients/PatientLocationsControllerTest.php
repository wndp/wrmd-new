<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Location;
use App\Models\Patient;
use App\Models\PatientLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientLocationsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_store_a_patient_location(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.location.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_a_patient_location(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.location.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_patient_before_storing_a_patient_location(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_a_new_patient_location(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-28', 'time_admitted_at' => '08:30']);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $admission->patient))
            ->assertInvalid([
                'moved_in_at' => 'The moved in date field is required.',
                'facility_id' => 'The facility field is required.',
                'area' => 'The area field is required when hash is not present.',
            ])
            ->assertValid('hash');

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => 'foo',
                'facility_id' => 123,
            ])
            ->assertInvalid([
                'moved_in_at' => 'The moved in date field must be a valid date.',
                'facility_id' => 'The selected facility is invalid.',
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2024-12-25',
            ])
            ->assertInvalid(['moved_in_at' => 'The moved in date field must be a date after or equal to 2024-12-28']);
    }

    public function test_it_fails_validation_when_trying_to_store_a_patient_location_with_a_location_hash(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $admission->patient), [
                'hash' => 'xxxx',
            ])
            ->assertInvalid(['hash' => 'validation.exists']) // For some reason validation.exists is not translating
            ->assertValid('area');
    }

    public function test_it_stores_a_patient_location_for_an_unknown_location(): void
    {
        $facilityId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-25']);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2024-12-28 17:17:00',
                'facility_id' => $facilityId,
                'area' => 'ICU',
                'enclosure' => 'Inc 1',
                'comments' => 'lorem ipsum',
            ])
            ->assertRedirect(route('dashboard'));

        $location = Location::where([
            'team_id' => $me->team->id,
            'facility_id' => $facilityId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ])->firstOrFail();

        $this->assertDatabaseHas('patient_locations', [
            'location_id' => $location->id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2024-12-29 01:17:00',
            'comments' => 'lorem ipsum',
        ]);
    }

    public function test_it_stores_a_patient_location_for_an_known_location(): void
    {
        $facilityId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-25']);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $location = Location::factory()->create([
            'team_id' => $me->team->id,
            'facility_id' => $facilityId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2024-12-28 17:17:00',
                'facility_id' => $facilityId,
                'area' => 'ICU',
                'enclosure' => 'Inc 1',
            ])
            ->assertRedirect(route('dashboard'));

        $locations = Location::all();
        $this->assertCount(1, $locations);
        $this->assertTrue($locations->first()->is($location));

        $this->assertDatabaseHas('patient_locations', [
            'location_id' => $location->id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2024-12-29 01:17:00',
        ]);
    }

    public function test_it_stores_a_patient_location_using_a_locations_hash(): void
    {
        $facilityId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-25']);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $location = Location::factory()->create([
            'team_id' => $me->team->id,
            'hash' => 'xxxx',
            'facility_id' => $facilityId,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2024-12-28 17:17:00',
                'facility_id' => $facilityId,
                'hash' => 'xxxx',
            ])
            ->assertRedirect(route('dashboard'));

        $locations = Location::all();
        $this->assertCount(1, $locations);
        $this->assertTrue($locations->first()->is($location));

        $this->assertDatabaseHas('patient_locations', [
            'patient_id' => $admission->patient_id,
            'location_id' => $location->id,
            'moved_in_at' => '2024-12-29 01:17:00',
        ]);
    }

    public function test_it_validates_ownership_of_a_patient_location_before_updating(): void
    {
        $facilityId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $patient = Patient::factory()->create(['date_admitted_at' => '2024-12-25']);
        $patientLocation = PatientLocation::factory()->create();

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$patient, $patientLocation]), [
                'moved_in_at' => '2024-12-26',
                'facility_id' => $facilityId,
                'area' => 'ICU',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_patient_location(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-28', 'time_admitted_at' => '08:30']);
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]))
            ->assertInvalid([
                'moved_in_at' => 'The moved in date field is required.',
                'facility_id' => 'The facility field is required.',
                'area' => 'The area field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]), [
                'moved_in_at' => 'foo',
                'facility_id' => 123,
            ])
            ->assertInvalid([
                'moved_in_at' => 'The moved in date field must be a valid date.',
                'facility_id' => 'The selected facility is invalid.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]), [
                'moved_in_at' => '2024-12-25',
            ])
            ->assertInvalid(['moved_in_at' => 'The moved in date field must be a date after or equal to 2024-12-28']);
    }

    public function test_it_updates_a_patient_location_into_a_new_location(): void
    {
        $facilityId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-25']);
        $patientLocation = PatientLocation::factory()->for(
            Location::factory()->create([
                'team_id' => $me->team->id,
                'facility_id' => $facilityId,
                'area' => 'ICU',
                'enclosure' => 'Incubator 1',
            ])
        )->create(['patient_id' => $admission->patient]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]), [
                'moved_in_at' => '2024-12-28 17:17:00',
                'facility_id' => $facilityId,
                'area' => 'ICU',
                'enclosure' => 'Incubator 2',
                'comments' => 'Test',
                'hours' => 1,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('locations', 2);

        $this->assertDatabaseHas('locations', [
            'team_id' => $me->team->id,
            'facility_id' => $facilityId,
            'area' => 'ICU',
            'enclosure' => 'Incubator 2',
        ]);

        $this->assertDatabaseHas('patient_locations', [
            'id' => $patientLocation->id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2024-12-29 01:17:00',
            'comments' => 'Test',
            'hours' => 1,
        ]);
    }

    public function test_it_updates_a_patient_location_using_the_same_location(): void
    {
        $facilityId = AttributeOption::factory()->create(['name' => AttributeOptionName::PATIENT_LOCATION_FACILITIES])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2024-12-25']);
        $patientLocation = PatientLocation::factory()->for(
            Location::factory()->create([
                'team_id' => $me->team->id,
                'facility_id' => $facilityId,
                'area' => 'ICU',
                'enclosure' => 'Incubator',
            ])
        )->create(['patient_id' => $admission->patient]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]), [
                'moved_in_at' => '2024-12-28 17:17:00',
                'facility_id' => $facilityId,
                'area' => 'ICU',
                'enclosure' => 'Incubator',
                'comments' => 'Test',
                'hours' => 1,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('locations', 1);
        $this->assertDatabaseHas('patient_locations', [
            'id' => $patientLocation->id,
            'location_id' => $patientLocation->location_id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2024-12-29 01:17:00',
            'comments' => 'Test',
            'hours' => 1,
        ]);
    }

    public function test_it_validates_ownership_of_a_patient_location_before_deleting(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $admission = $this->createCase($me->team);
        $patientLocation = PatientLocation::factory()->create();

        $this->actingAs($me->user)
            ->delete(route('patients.location.destroy', [$admission->patient, $patientLocation]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_a_patient_location(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_LOCATIONS->value);

        $admission = $this->createCase($me->team);
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $admission->patient]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->delete(route('patients.location.destroy', [$admission->patient, $patientLocation]))
            ->assertRedirect(route('dashboard'));

        $this->assertSoftDeleted('patient_locations', [
            'id' => $patientLocation->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
