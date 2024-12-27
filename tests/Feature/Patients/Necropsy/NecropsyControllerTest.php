<?php

namespace Tests\Feature\Patients\Necropsy;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Necropsy\Necropsy;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class NecropsyControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_access_necropsy(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.necropsy.edit', $patient))->assertRedirect('login');
    }

    public function test_it_displays_the_necropsy_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);

        $this->actingAs($me->user)->get(route('patients.necropsy.edit'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Necropsy/Edit')
                    ->where('admission.patient_id', $admission->patient_id)
            );
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_necropsy(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_necropsy(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $admission->patient))
            ->assertHasValidationError('prosector', 'The prosector field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $admission->patient))
            ->assertHasValidationError('necropsied_at', 'The necropsy date field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.necropsy.update', $admission->patient), [
                'necropsied_at' => 'foo',
            ])
            ->assertHasValidationError('necropsied_at', 'The necropsy date is not a valid date.');
    }

    public function test_it_saves_a_new_necropsy(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.update', $admission->patient), [
                'necropsied_at' => '2023-06-06 12:35',
                'prosector' => 'Jim Halpert',
                'is_photos_collected' => 1,
                'is_carcass_radiographed' => 0,
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'patient_id' => $admission->patient_id,
            'necropsied_at' => '2023-06-06 19:35:00',
            'prosector' => 'Jim Halpert',
            'is_photos_collected' => 1,
            'is_carcass_radiographed' => 0,
        ]);
    }

    public function test_it_updates_an_existing_necropsy(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $necropsy = Necropsy::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-necropsy');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.necropsy.update', $admission->patient), [
                'necropsied_at' => '2023-06-06 12:35:00',
                'prosector' => 'Jim Halpert',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('necropsies', [
            'id' => $necropsy->id,
            'necropsied_at' => '2023-06-06 19:35:00',
            'prosector' => 'Jim Halpert',
        ]);
    }
}
