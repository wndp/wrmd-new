<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Necropsy\Necropsy;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class NecropsySummaryControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_necropsy_summary(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.necropsy.summary.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_necropsy_summary(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.necropsy.summary.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy_summary(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.summary.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_saves_a_new_necropsy_summary(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.summary.update', $admission->patient), [
                'samples_collected' => ['Heart', 'Lung', 'Other'],
                'other_sample' => 'toes',
                'morphologic_diagnosis' => 'lorem ipsum',
                'gross_summary_diagnosis' => 'dolor Etiam',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'samples_collected' => json_encode(['Heart', 'Lung', 'Other']),
            'other_sample' => 'toes',
            'morphologic_diagnosis' => 'lorem ipsum',
            'gross_summary_diagnosis' => 'dolor Etiam',
        ]);
    }

    public function test_it_updates_an_existing_necropsy_summary(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

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
