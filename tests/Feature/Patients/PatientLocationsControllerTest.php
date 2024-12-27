<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Location;
use App\Domain\Patients\Patient;
use App\Domain\Patients\PatientLocation;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class PatientLocationsControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_store_a_patient_location(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.location.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_a_patient_location(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.location.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_patient_before_storing(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_a_patient_location(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $admission->patient))
            ->assertHasValidationError('moved_in_at', 'The moved in at date field is required.')
            ->assertHasValidationError('facility', 'The facility field is required.')
            ->assertHasValidationError('area', 'The area field is required.');

        $this->actingAs($me->user)
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => 'foo',
                'hash' => 'xxxx',
            ])
            ->assertHasValidationError('moved_in_at', 'The moved in at date is not a valid date.')
            ->assertValidationErrorMissing('area')
            ->assertHasValidationError('hash', 'The provided hash does not exist.');
    }

    public function test_it_stores_a_patient_location_for_an_unknown_location(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2017-02-13 08:43:00',
                'facility' => 'Clinic',
                'area' => 'ICU',
                'enclosure' => 'Inc 1',
            ])
            ->assertRedirect(route('dashboard'));

        $location = Location::where([
            'account_id' => $me->account->id,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ])->firstOrFail();

        $this->assertDatabaseHas('patient_locations', [
            'location_id' => $location->id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2017-02-13 16:43:00',
            'facility' => 'Clinic',
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);
    }

    public function test_it_stores_a_patient_location_for_an_known_location(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('manage-locations');

        $location = Location::factory()->create([
            'account_id' => $me->account->id,
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2017-02-13 08:43:00',
                'facility' => 'Clinic',
                'area' => 'ICU',
                'enclosure' => 'Inc 1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patient_locations', [
            'location_id' => $location->id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2017-02-13 16:43:00',
            'facility' => 'Clinic',
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);
    }

    public function test_it_stores_a_location_if_a_hash_is_given(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('manage-locations');

        $location = Location::factory()->create([
            'account_id' => $me->account->id,
            'hash' => 'xxxx',
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.location.store', $admission->patient), [
                'moved_in_at' => '2017-02-13 08:43:00',
                'facility' => 'Clinic',
                'hash' => 'xxxx',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patient_locations', [
            'patient_id' => $admission->patient_id,
            'location_id' => $location->id,
            'moved_in_at' => '2017-02-13 16:43:00',
            'facility' => 'Clinic',
            'area' => 'ICU',
            'enclosure' => 'Inc 1',
        ]);
    }

    public function test_it_validates_ownership_of_a_patient_location_before_updating(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $patientLocation = PatientLocation::factory()->create();
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$patient, $patientLocation]), [
                'moved_in_at' => '2017-02-13 08:43:00',
                'facility' => 'Clinic',
                'area' => 'ICU',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_patient_location(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]))
            ->assertHasValidationError('moved_in_at', 'The moved in at date field is required.')
            ->assertHasValidationError('facility', 'The facility field is required.')
            ->assertHasValidationError('area', 'The area field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]), [
                'moved_in_at' => 'foo',
            ])
            ->assertHasValidationError('moved_in_at', 'The moved in at date is not a valid date.');
    }

    public function test_it_updates_a_patient_location(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.location.update', [$admission->patient, $patientLocation]), [
                'moved_in_at' => '2017-02-13 08:43:00',
                'facility' => 'Clinic',
                'area' => 'ICU',
                'enclosure' => 'Incubator',
                'comments' => 'Test',
                'hours' => 1,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patient_locations', [
            'id' => $patientLocation->id,
            'patient_id' => $admission->patient_id,
            'moved_in_at' => '2017-02-13 16:43:00',
            'facility' => 'Clinic',
            'area' => 'ICU',
            'enclosure' => 'Incubator',
            'comments' => 'Test',
            'hours' => 1,
        ]);
    }

    public function test_it_validates_ownership_of_a_patient_location_before_deleting(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $patientLocation = PatientLocation::factory()->create();
        BouncerFacade::allow($me->user)->to('manage-locations');

        $this->actingAs($me->user)
            ->delete(route('patients.location.destroy', [$admission->patient, $patientLocation]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_a_patient_location(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $patientLocation = PatientLocation::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to('manage-locations');

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
