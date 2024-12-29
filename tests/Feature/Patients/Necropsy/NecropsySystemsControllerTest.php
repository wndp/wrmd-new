<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Models\Necropsy;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class NecropsySystemsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_necropsy_systems(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.necropsy.systems.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_necropsy_systems(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.necropsy.systems.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy_systems(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.systems.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_necropsy_systems(): void
    {
        $bodyPartFindingId = AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_BODY_PART_FINDINGS])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.systems.update', $admission->patient), [
                'integument_finding_id' => $bodyPartFindingId,
                'integument' => 'lorem',
                'cavities_finding_id' => $bodyPartFindingId,
                'cavities' => 'ipsum',
                'gastrointestinal_finding_id' => $bodyPartFindingId,
                'gastrointestinal' => 'dolor',
                'liver_gallbladder_finding_id' => $bodyPartFindingId,
                'liver_gallbladder' => 'emit',
                'hematopoietic_finding_id' => $bodyPartFindingId,
                'hematopoietic' => 'Donec',
                'renal_finding_id' => $bodyPartFindingId,
                'renal' => 'elit',
                'respiratory_finding_id' => $bodyPartFindingId,
                'respiratory' => 'libero',
                'cardiovascular_finding_id' => $bodyPartFindingId,
                'cardiovascular' => 'sodales',
                'endocrine_reproductive_finding_id' => $bodyPartFindingId,
                'endocrine_reproductive' => 'nec',
                'nervous_finding_id' => $bodyPartFindingId,
                'nervous' => 'Aenean',
                'head_finding_id' => $bodyPartFindingId,
                'head' => 'massa',
                'musculoskeletal_finding_id' => $bodyPartFindingId,
                'musculoskeletal' => 'Nulla',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'integument_finding_id' => $bodyPartFindingId,
            'integument' => 'lorem',
            'cavities_finding_id' => $bodyPartFindingId,
            'cavities' => 'ipsum',
            'gastrointestinal_finding_id' => $bodyPartFindingId,
            'gastrointestinal' => 'dolor',
            'liver_gallbladder_finding_id' => $bodyPartFindingId,
            'liver_gallbladder' => 'emit',
            'hematopoietic_finding_id' => $bodyPartFindingId,
            'hematopoietic' => 'Donec',
            'renal_finding_id' => $bodyPartFindingId,
            'renal' => 'elit',
            'respiratory_finding_id' => $bodyPartFindingId,
            'respiratory' => 'libero',
            'cardiovascular_finding_id' => $bodyPartFindingId,
            'cardiovascular' => 'sodales',
            'endocrine_reproductive_finding_id' => $bodyPartFindingId,
            'endocrine_reproductive' => 'nec',
            'nervous_finding_id' => $bodyPartFindingId,
            'nervous' => 'Aenean',
            'head_finding_id' => $bodyPartFindingId,
            'head' => 'massa',
            'musculoskeletal_finding_id' => $bodyPartFindingId,
            'musculoskeletal' => 'Nulla',
        ]);
    }

    public function test_it_updates_an_existing_necropsy_systems(): void
    {
        $bodyPartFindingId = AttributeOption::factory()->create(['name' => AttributeOptionName::EXAM_BODY_PART_FINDINGS])->id;

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.systems.update', $admission->patient), [
                'integument_finding_id' => $bodyPartFindingId,
                'integument' => 'lorem',
                'cavities_finding_id' => $bodyPartFindingId,
                'cavities' => 'ipsum',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'integument_finding_id' => $bodyPartFindingId,
            'integument' => 'lorem',
            'cavities_finding_id' => $bodyPartFindingId,
            'cavities' => 'ipsum',
        ]);
    }
}
