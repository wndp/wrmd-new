<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\Extension;
use App\Models\AttributeOption;
use App\Models\Necropsy;
use App\Models\Patient;
use App\Support\ExtensionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class NecropsyControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_necropsy(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.necropsy.edit', $patient))->assertRedirect('login');
    }

    public function test_if_the_necropsy_extension_is_not_active_the_page_wont_load()
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();

        $this->actingAs($me->user)->get(route('patients.necropsy.edit', $patient))->assertForbidden();
    }

    public function test_it_displays_the_necropsy_view(): void
    {
        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::NECROPSY);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)->get(route('patients.necropsy.edit'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Necropsy/Edit')
                    ->has('necropsySampleOtherId')
                    ->where('patient.id', $admission->patient_id)
            );
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $patient = Patient::factory()->create();

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $patient), [
                'prosector' => 'Jim',
                'necropsied_at' => '2024-12-28',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_necropsy(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $admission->patient))
            ->assertInvalid([
                'prosector' => 'The prosector field is required.',
                'necropsied_at' => 'The necropsy date field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $admission->patient), [
                'necropsied_at' => 'foo',
                'carcass_condition_id' => 123,
                'is_photos_collected' => 'foo',
                'is_carcass_radiographed' => 'foo',
                'is_previously_frozen' => 'foo',
                'is_scavenged' => 'foo',
                'is_discarded' => 'foo',
            ])
            ->assertInvalid([
                'necropsied_at' => 'The necropsy date field must be a valid date.',
                'carcass_condition_id' => 'The selected carcass condition is invalid.',
                'is_photos_collected' => 'The photos collected field must be true or false',
                'is_carcass_radiographed' => 'The carcass radiographed field must be true or false.',
                'is_previously_frozen' => 'The previously frozen field must be true or false.',
                'is_scavenged' => 'The scavenged field must be true or false.',
                'is_discarded' => 'The discarded field must be true or false.',
            ]);
    }

    public function test_it_saves_a_new_necropsy(): void
    {
        $carcassConditionIds = AttributeOption::factory()->count(2)->create(['name' => AttributeOptionName::NECROPSY_CARCASS_CONDITIONS]);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.update', $admission->patient), [
                'necropsied_at' => '2023-06-06 12:35',
                'prosector' => 'Jim Halpert',
                'carcass_condition_id' => $carcassConditionIds->first()->id,
                'is_photos_collected' => 1,
                'is_carcass_radiographed' => 0,
                'is_previously_frozen' => 0,
                'is_scavenged' => 1,
                'is_discarded' => 0,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'date_necropsied_at' => '2023-06-06',
            'time_necropsied_at' => '19:35:00',
            'prosector' => 'Jim Halpert',
            'carcass_condition_id' => $carcassConditionIds->first()->id,
            'is_photos_collected' => 1,
            'is_carcass_radiographed' => 0,
            'is_previously_frozen' => 0,
            'is_scavenged' => 1,
            'is_discarded' => 0,
        ]);
    }

    public function test_it_updates_an_existing_necropsy(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $admission = $this->createCase($me->team);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.update', $admission->patient), [
                'necropsied_at' => '2023-06-06 12:35:00',
                'prosector' => 'Jim Halpert',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'date_necropsied_at' => '2023-06-06',
            'time_necropsied_at' => '19:35:00',
            'prosector' => 'Jim Halpert',
        ]);
    }
}
