<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Necropsy\Necropsy;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class NecropsySystemsControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_necropsy_systems(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.necropsy.systems.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_necropsy_systems(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.necropsy.systems.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy_systems(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.systems.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_necropsy_systems(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.systems.update', $admission->patient), [
                'integument_finding' => 'Abnormal',
                'integument' => 'lorem',
                'cavities_finding' => 'Abnormal',
                'cavities' => 'ipsum',
                'gastrointestinal_finding' => 'Abnormal',
                'gastrointestinal' => 'dolor',
                'liver_gallbladder_finding' => 'Abnormal',
                'liver_gallbladder' => 'emit',
                'hematopoietic_finding' => 'Abnormal',
                'hematopoietic' => 'Donec',
                'renal_finding' => 'Abnormal',
                'renal' => 'elit',
                'respiratory_finding' => 'Abnormal',
                'respiratory' => 'libero',
                'cardiovascular_finding' => 'Abnormal',
                'cardiovascular' => 'sodales',
                'endocrine_reproductive_finding' => 'Abnormal',
                'endocrine_reproductive' => 'nec',
                'nervous_finding' => 'Abnormal',
                'nervous' => 'Aenean',
                'head_finding' => 'Abnormal',
                'head' => 'massa',
                'musculoskeletal_finding' => 'Abnormal',
                'musculoskeletal' => 'Nulla',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'integument_finding' => 'Abnormal',
            'integument' => 'lorem',
            'cavities_finding' => 'Abnormal',
            'cavities' => 'ipsum',
            'gastrointestinal_finding' => 'Abnormal',
            'gastrointestinal' => 'dolor',
            'liver_gallbladder_finding' => 'Abnormal',
            'liver_gallbladder' => 'emit',
            'hematopoietic_finding' => 'Abnormal',
            'hematopoietic' => 'Donec',
            'renal_finding' => 'Abnormal',
            'renal' => 'elit',
            'respiratory_finding' => 'Abnormal',
            'respiratory' => 'libero',
            'cardiovascular_finding' => 'Abnormal',
            'cardiovascular' => 'sodales',
            'endocrine_reproductive_finding' => 'Abnormal',
            'endocrine_reproductive' => 'nec',
            'nervous_finding' => 'Abnormal',
            'nervous' => 'Aenean',
            'head_finding' => 'Abnormal',
            'head' => 'massa',
            'musculoskeletal_finding' => 'Abnormal',
            'musculoskeletal' => 'Nulla',
        ]);
    }

    public function test_it_updates_an_existing_necropsy_systems(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.systems.update', $admission->patient), [
                'integument_finding' => 'Abnormal',
                'integument' => 'lorem',
                'cavities_finding' => 'Abnormal',
                'cavities' => 'ipsum',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'integument_finding' => 'Abnormal',
            'integument' => 'lorem',
            'cavities_finding' => 'Abnormal',
            'cavities' => 'ipsum',
        ]);
    }
}
