<?php

namespace Tests\Feature\Patients;

use App\Domain\Cache\PatientSelector;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class PatientsControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_list_patients(): void
    {
        $this->get(route('patients.index'))->assertRedirect('login');
    }

    public function test_it_displays_the_patient_index_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);

        $this->actingAs($me->user)->get(route('patients.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Index')
                    ->hasAll(['lists', 'list', 'listFigures', 'hasQueryCache'])
                    ->where('list', 'patients-this-year-list')
                    ->where('listFigures.rows.data.0.patient_id', $admission->patient_id)
            );
    }

    public function test_un_authenticated_users_cant_create_patients(): void
    {
        $this->get(route('patients.create'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_create_patients(): void
    {
        $me = $this->createAccountUser();
        $this->actingAs($me->user)->get(route('patients.create'))->assertForbidden();
    }

    public function test_it_displays_the_patient_create_view(): void
    {
        $me = $this->createAccountUser();
        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)->get(route('patients.create'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Create')
                    ->hasOptions(['availableYears', 'actionsAfterStore'])
            );
    }

    public function test_it_fails_validation_when_trying_to_store_a_newly_created_case_in_storage(): void
    {
        $me = $this->createAccountUser();
        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)
            ->post(route('patients.store'))
            ->assertHasValidationError('common_name', 'The common name field is required.')
            ->assertHasValidationError('admitted_at', 'The date admitted field is required.')
            ->assertHasValidationError('case_year', 'The case year field is required.')
            ->assertHasValidationError('admitted_by', 'The admitted by field is required.')
            ->assertHasValidationError('address_found', 'The address found field is required.')
            ->assertHasValidationError('city_found', 'The city found field is required.')
            ->assertHasValidationError('subdivision_found', 'The subdivision found field is required.')
            ->assertHasValidationError('found_at', 'The date found field is required.')
            ->assertHasValidationError('reasons_for_admission', 'The reasons for admission field is required.');

        $this->actingAs($me->user)->post(route('patients.store'), [
            'common_name' => 'x',
            'admitted_at' => 'x',
            'case_year' => 'x',
            'rescuer' => 'x',
            'admitted_by' => 'x',
            'address_found' => 'x',
            'city_found' => 'x',
            'subdivision_found' => 'x',
            'found_at' => 'x',
            'reasons_for_admission' => 'x',
        ])
            ->assertHasValidationError('admitted_at', 'The date admitted is not a valid date.')
            ->assertHasValidationError('case_year', 'The selected case year is invalid.')
            ->assertHasValidationError('found_at', 'The date found is not a valid date.');
    }

    public function test_it_admits_a_new_patient(): void
    {
        $me = $this->createAccountUser();
        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)->post(route('patients.store'), [
            'action_after_store' => 'return',
            'common_name' => 'finch',
            'morph' => 'red',
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reasons_for_admission' => 'sick',
            'lat_found' => 12.345,
            'lng_found' => 67.89,
        ])
            ->assertRedirect(route('patients.create'))
            ->assertHasFlashMessage('Patient '.date('y').'-1 created.');

        $this->assertDatabaseHas('admissions', [
            'account_id' => $me->account->id,
            'case_year' => date('Y'),
            'case_id' => 1,
        ]);

        $this->assertDatabaseHas('patients', [
            'taxon_id' => 999999,
            'common_name' => 'finch',
            'morph' => 'red',
            'admitted_at' => date('Y').'-04-24 15:53:00',
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reasons_for_admission' => 'sick',
        ]);
    }

    public function test_it_admits_a_newly_created_patient_to_a_different_year_than_this_year(): void
    {
        $me = $this->createAccountUser();
        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)->post(route('patients.store'), [
            'action_after_store' => 'return',
            'common_name' => 'finch',
            'morph' => 'red',
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y') - 1,
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reasons_for_admission' => 'sick',
        ])
            ->assertRedirect(route('patients.create'))
            ->assertHasFlashMessage('Patient '.(date('y') - 1).'-1 created.');

        $this->assertDatabaseHas('admissions', [
            'account_id' => $me->account->id,
            'case_year' => date('Y') - 1,
            'case_id' => 1,
        ]);
    }

    public function test_it_admitts_multiple_patients_and_redirects_to_view_the_first_one(): void
    {
        $me = $this->createAccountUser();
        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)->post(route('patients.store'), [
            'action_after_store' => 'view',
            'common_name' => 'finch',
            'morph' => 'red',
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reasons_for_admission' => 'sick',
            'cases_to_create' => 2,
        ])
            ->assertRedirect(route('patients.initial.edit', ['y' => date('Y'), 'c' => 1]))
            ->assertHasFlashMessage('Patients '.date('y').'-1 through '.date('y').'-2 created.');

        $this->assertDatabaseHas('admissions', [
            'account_id' => $me->account->id,
            'case_year' => date('Y'),
            'case_id' => 1,
        ]);

        $this->assertDatabaseHas('admissions', [
            'account_id' => $me->account->id,
            'case_year' => date('Y'),
            'case_id' => 2,
        ]);

        $this->assertDatabaseHas('patients', [
            'taxon_id' => 999999,
            'common_name' => 'finch',
            'morph' => 'red',
            'admitted_at' => date('Y').'-04-24 15:53:00',
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reasons_for_admission' => 'sick',
        ]);
    }

    public function test_it_admitts_multiple_patients_and_redirects_to_batch_update_them(): void
    {
        $me = $this->createAccountUser();
        BouncerFacade::allow($me->user)->to('create-patients');

        $this->actingAs($me->user)->post(route('patients.store'), [
            'action_after_store' => 'batch',
            'common_name' => 'finch',
            'morph' => 'red',
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reasons_for_admission' => 'sick',
            'cases_to_create' => 2,
        ])
            ->assertRedirect(route('patients.batch.edit'))
            ->assertHasFlashMessage('Patients '.date('y').'-1 through '.date('y').'-2 created.');

        $this->assertEquals(2, PatientSelector::count());
    }
}
