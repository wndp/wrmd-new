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

final class NecropsySummaryControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_necropsy_summary(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.necropsy.summary.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_necropsy_summary(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.necropsy.summary.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy_summary(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.summary.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_necropsy_summary(): void
    {
        $samples = AttributeOption::factory()->count(3)->create(['name' => AttributeOptionName::NECROPSY_SAMPLES]);

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.summary.update', $admission->patient), [
                'samples_collected' => $samples->pluck('id')->toArray(),
                'other_sample' => 'toes',
                'morphologic_diagnosis' => 'lorem ipsum',
                'gross_summary_diagnosis' => 'dolor Etiam',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'samples_collected' => json_encode($samples->pluck('id')->toArray()),
            'other_sample' => 'toes',
            'morphologic_diagnosis' => 'lorem ipsum',
            'gross_summary_diagnosis' => 'dolor Etiam',
        ]);
    }

    public function test_it_updates_an_existing_necropsy_summary(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_NECROPSY->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.summary.update', $admission->patient), [
                'morphologic_diagnosis' => 'lorem ipsum',
                'gross_summary_diagnosis' => 'dolor Etiam',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'morphologic_diagnosis' => 'lorem ipsum',
            'gross_summary_diagnosis' => 'dolor Etiam',
        ]);
    }
}
