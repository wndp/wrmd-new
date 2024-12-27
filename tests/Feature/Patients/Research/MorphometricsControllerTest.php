<?php

namespace Tests\Feature\Patients\Research;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Research\Models\Morphometric;
use Carbon\Carbon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class MorphometricsControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_morphometrics(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.research.morphometrics.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_morphometrics(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.research.morphometrics.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_morphometrics(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.morphometrics.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_morphometrics(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.morphometrics.update', $admission->patient))
            ->assertHasValidationError('measured_at', 'The date measured field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.research.morphometrics.update', $admission->patient), [
                'measured_at' => 'foo',
            ])
            ->assertHasValidationError('measured_at', 'The date measured is not a valid date.');

        $this->actingAs($me->user)
            ->put(route('patients.research.morphometrics.update', $admission->patient), [
                'measured_at' => Carbon::now()->subDays(30),
            ])
            ->assertHasValidationError('measured_at');
    }

    public function test_it_saves_a_new_morphometrics(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2023-06-01']);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.morphometrics.update', $admission->patient), [
                'measured_at' => '2023-06-06',
                'bill_length' => '11',
                'bill_width' => '22',
                'bill_depth' => '33',
                'head_bill_length' => '44',
                'culmen' => '55',
                'exposed_culmen' => '66',
                'wing_chord' => '77',
                'flat_wing' => '88',
                'tarsus_length' => '99',
                'middle_toe_length' => '111',
                'toe_pad_length' => '222',
                'hallux_length' => '333',
                'tail_length' => '444',
                'weight' => '555',
                'samples_collected' => ['Feathers', 'Blood'],
                'remarks' => 'test2',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('morphometrics', [
            'patient_id' => $admission->patient_id,
            'measured_at' => '2023-06-06',
            'bill_length' => '11',
            'bill_width' => '22',
            'bill_depth' => '33',
            'head_bill_length' => '44',
            'culmen' => '55',
            'exposed_culmen' => '66',
            'wing_chord' => '77',
            'flat_wing' => '88',
            'tarsus_length' => '99',
            'middle_toe_length' => '111',
            'toe_pad_length' => '222',
            'hallux_length' => '333',
            'tail_length' => '444',
            'weight' => '555',
            'samples_collected' => json_encode(['Feathers', 'Blood']),
            'remarks' => 'test2',
        ]);
    }

    public function test_it_updates_an_existing_morphometrics(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2023-06-01']);
        $morphometric = Morphometric::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.morphometrics.update', $admission->patient), [
                'measured_at' => '2023-06-06',
                'remarks' => 'lorem ipsum',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('morphometrics', [
            'id' => $morphometric->id,
            'measured_at' => '2023-06-06',
            'remarks' => 'lorem ipsum',
        ]);
    }
}
