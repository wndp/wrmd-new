<?php

namespace Tests\Feature\Admissions;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class DuplicationControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    private $me;

    private $pendingDispositionUiBehavior;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pendingDispositionUiBehavior = $this->createUiBehavior(
            AttributeOptionName::PATIENT_DISPOSITIONS,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING
        );

        $this->me = $this->createTeamUser();
    }

    public function test_un_authenticated_users_cant_duplicate_patients(): void
    {
        $this->get(route('patients.duplicate.create'))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_duplicate_patients(): void
    {
        $this->actingAs($this->me->user)->get(route('patients.duplicate.create'))->assertForbidden();
    }

    public function test_it_shows_the_form_for_duplicating_an_existing_patient(): void
    {
        BouncerFacade::allow($this->me->user)->to(Ability::CREATE_PATIENTS->value);

        $this->createCase($this->me->team, date('Y'));

        $this->actingAs($this->me->user)->get(route('patients.duplicate.create'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Duplicate')
                    ->hasOption('availableYears')
            );
    }

    public function test_it_fails_validation_when_trying_to_replicate_an_existing_patient(): void
    {
        BouncerFacade::allow($this->me->user)->to(Ability::CREATE_PATIENTS->value);

        $admission = $this->createCase($this->me->team, date('Y'));

        $this->actingAs($this->me->user)->post(route('patients.duplicate.store', $admission->patient))
            ->assertInvalid([
                'admitted_at' => 'The date admitted field is required.',
                'case_year' => 'The case year field is required.',
                'admitted_by' => 'The admitted by field is required.',
                'address_found' => 'The address found field is required.',
                'city_found' => 'The city found field is required.',
                'subdivision_found' => 'The subdivision found field is required.',
                'found_at' => 'The date found field is required.',
                'reason_for_admission' => 'The reason for admission field is required.',
            ]);

        $this->actingAs($this->me->user)->post(route('patients.duplicate.store', $admission->patient), [
            'commonName' => 'x',
            'admitted_at' => 'x',
            'case_year' => 'x',
            //'rescuer' => 'x',
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
                'found_at' => 'The date found is not a valid date.',
            ]);
    }

    public function test_it_replicates_an_existing_patient(): void
    {
        \Illuminate\Support\Facades\Cache::flush();

        BouncerFacade::allow($this->me->user)->to(Ability::CREATE_PATIENTS->value);

        $admission = $this->createCase($this->me->team, date('Y'), [
            'common_name' => 'finch',
            'disposition_id' => $this->pendingDispositionUiBehavior->attribute_option_id,
        ]);

        $this->actingAs($this->me->user)->post(route('patients.duplicate.store', $admission->patient), [
            'admitted_at' => date('Y').'-04-24 09:14:00',
            'case_year' => date('Y'),
            'rescuer' => ['id' => null],
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
        ])
            ->assertRedirect('patients/initial?y='.date('Y').'&c=2');

        $this->assertDatabaseHas('admissions', [
            'team_id' => $this->me->team->id,
            'case_year' => date('Y'),
            'case_id' => 2,
        ]);

        $this->assertDatabaseHas('patients', [
            'common_name' => 'finch',
            'date_admitted_at' => date('Y').'-04-24',
            'time_admitted_at' => '16:14:00',
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => date('Y').'-04-24',
            'reason_for_admission' => 'sick',
            'disposition_id' => $this->pendingDispositionUiBehavior->attribute_option_id,
        ]);
    }

    public function test_it_replicates_an_existing_case_into_a_different_year_than_this_year(): void
    {
        BouncerFacade::allow($this->me->user)->to(Ability::CREATE_PATIENTS->value);

        $thisYear = date('Y');
        $lastYear = date('Y') - 1;

        $admission = $this->createCase($this->me->team, $lastYear, [
            'common_name' => 'finch',
            'disposition_id' => $this->pendingDispositionUiBehavior->attribute_option_id,
        ]);

        $this->actingAs($this->me->user)->post(route('patients.duplicate.store', $admission->patient), [
            'admitted_at' => $lastYear.'-04-24 09:14:00',
            'case_year' => $thisYear,
            'rescuer' => ['id' => null],
            'morph' => 'red',
            'admitted_by' => 'devin',
            'transported_by' => 'rachel',
            'address_found' => '123 main st',
            'city_found' => 'lower lake',
            'subdivision_found' => 'CA',
            'found_at' => $lastYear.'-04-24',
            'reason_for_admission' => 'sick',
            'disposition_id' => $this->pendingDispositionUiBehavior->attribute_option_id,
        ])
            ->assertRedirect('patients/initial?y='.$thisYear.'&c=1');

        $this->assertDatabaseHas('admissions', [
            'team_id' => $this->me->team->id,
            'case_year' => $thisYear,
            'case_id' => 1,
        ]);
    }
}
