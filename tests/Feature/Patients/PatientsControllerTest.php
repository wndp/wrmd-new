<?php

namespace Tests\Feature\Patients;

use App\Caches\PatientSelector;
use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class PatientsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    public function test_un_authenticated_users_cant_list_patients(): void
    {
        $this->get(route('patients.index'))->assertRedirect('login');
    }

    public function test_it_displays_the_patient_index_view(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);

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
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('patients.create'))->assertForbidden();
    }

    public function test_it_displays_the_patient_create_view(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->actingAs($me->user)->get(route('patients.create'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Create')
                    ->hasOptions(['availableYears', 'actionsAfterStore'])
            );
    }

    public function test_it_fails_validation_when_trying_to_store_a_new_patient_in_storage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->actingAs($me->user)
            ->post(route('patients.store'))
            ->assertInvalid([
                'common_name' => 'The common name field is required.',
                'admitted_at' => 'The date admitted field is required.',
                'case_year' => 'The case year field is required.',
                'admitted_by' => 'The admitted by field is required.',
                'address_found' => 'The address found field is required.',
                'city_found' => 'The city found field is required.',
                'subdivision_found' => 'The subdivision found field is required.',
                'found_at' => 'The date found field is required.',
                'reason_for_admission' => 'The reason for admission field is required.'
            ]);

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
            'reason_for_admission' => 'x',
        ])
            ->assertInvalid([
                'admitted_at' => 'The date admitted is not a valid date.',
                'case_year' => 'The selected case year is invalid.',
                'found_at' => 'The date found is not a valid date.'
            ]);
    }

    public function test_it_admits_a_new_patient(): void
    {
        $this->pendingDispositionId();
        $taxaMorphId = AttributeOption::factory()->create(['name' => AttributeOptionName::TAXA_MORPHS])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->actingAs($me->user)->post(route('patients.store'), [
            'cases_to_create' => 1,
            'no_solicitations' => true,
            'action_after_store' => 'return',
            'common_name' => 'finch',
            'morph_id' => $taxaMorphId,
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
            'lat_found' => 12.345,
            'lng_found' => 67.89,
        ])
            ->assertRedirect(route('patients.create'))
            ->assertHasNotificationMessage('Patient '.date('y').'-1 created.');

        $this->assertDatabaseHas('admissions', [
            'team_id' => $me->team->id,
            'case_year' => date('Y'),
            'case_id' => 1,
        ]);

        $this->assertDatabaseHas('patients', [
            'taxon_id' => null,
            'common_name' => 'finch',
            'morph_id' => $taxaMorphId,
            'date_admitted_at' => date('Y').'-04-24',
            'time_admitted_at' => '15:53:00',
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
        ]);
    }

    public function test_it_admits_a_new_patient_to_a_different_year_than_this_year(): void
    {
        $this->pendingDispositionId();
        $taxaMorphId = AttributeOption::factory()->create(['name' => AttributeOptionName::TAXA_MORPHS])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->actingAs($me->user)->post(route('patients.store'), [
            'cases_to_create' => 1,
            'no_solicitations' => true,
            'action_after_store' => 'return',
            'common_name' => 'finch',
            'morph_id' => $taxaMorphId,
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y') - 1,
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
        ])
            ->assertRedirect(route('patients.create'))
            ->assertHasNotificationMessage('Patient '.(date('y') - 1).'-1 created.');

        $this->assertDatabaseHas('admissions', [
            'team_id' => $me->team->id,
            'case_year' => date('Y') - 1,
            'case_id' => 1,
        ]);
    }

    public function test_it_admits_multiple_patients_and_redirects_to_view_the_first_one(): void
    {
        $this->pendingDispositionId();
        $taxaMorphId = AttributeOption::factory()->create(['name' => AttributeOptionName::TAXA_MORPHS])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->actingAs($me->user)->post(route('patients.store'), [
            'cases_to_create' => 1,
            'no_solicitations' => true,
            'action_after_store' => 'view',
            'common_name' => 'finch',
            'morph_id' => $taxaMorphId,
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
            'cases_to_create' => 2,
        ])
            ->assertRedirect(route('patients.initial.edit', ['y' => date('Y'), 'c' => 1]))
            ->assertHasNotificationMessage('Patients '.date('y').'-1 through '.date('y').'-2 created.');

        $this->assertDatabaseHas('admissions', [
            'team_id' => $me->team->id,
            'case_year' => date('Y'),
            'case_id' => 1,
        ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $me->team->id,
            'case_year' => date('Y'),
            'case_id' => 2,
        ]);

        $this->assertDatabaseHas('patients', [
            'taxon_id' => null,
            'common_name' => 'finch',
            'morph_id' => $taxaMorphId,
            'date_admitted_at' => date('Y').'-04-24',
            'time_admitted_at' => '15:53:00',
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
        ]);
    }

    public function test_it_admitts_multiple_patients_and_redirects_to_batch_update_them(): void
    {
        $this->pendingDispositionId();
        $taxaMorphId = AttributeOption::factory()->create(['name' => AttributeOptionName::TAXA_MORPHS])->id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->actingAs($me->user)->post(route('patients.store'), [
            'cases_to_create' => 1,
            'no_solicitations' => true,
            'action_after_store' => 'batch',
            'common_name' => 'finch',
            'morph_id' => $taxaMorphId,
            'admitted_at' => date('Y').'-04-24 08:53:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
            'cases_to_create' => 2,
        ])
            ->assertRedirect(route('patients.batch.edit'))
            ->assertHasNotificationMessage('Patients '.date('y').'-1 through '.date('y').'-2 created.');

        $this->assertEquals(2, PatientSelector::count());
    }
}
