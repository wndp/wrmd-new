<?php

namespace Tests\Feature\Patients\Research;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use App\Extensions\Research\Models\Banding;
use Carbon\Carbon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class RecaptureControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_recapture(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.research.recapture.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_recapture(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.research.recapture.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_recapture(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.recapture.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_recapture(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.recapture.update', $admission->patient))
            ->assertHasValidationError('recaptured_at', 'The recapture date field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.research.recapture.update', $admission->patient), [
                'recaptured_at' => 'foo',
            ])
            ->assertHasValidationError('recaptured_at', 'The recapture date is not a valid date.');

        $this->actingAs($me->user)
            ->put(route('patients.research.recapture.update', $admission->patient), [
                'recaptured_at' => Carbon::now()->subDays(30),
            ])
            ->assertHasValidationError('recaptured_at');
    }

    public function test_it_saves_a_new_recapture(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2023-06-01']);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.recapture.update', $admission->patient), [
                'recaptured_at' => '2023-06-06',
                'recapture_disposition' => '1',
                'present_condition' => '00',
                'how_present_condition' => 'R',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'patient_id' => $admission->patient_id,
            'recaptured_at' => '2023-06-06',
            'recapture_disposition' => '1',
            'present_condition' => '00',
            'how_present_condition' => 'R',
        ]);
    }

    public function test_it_updates_an_existing_recapture(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2023-06-01']);
        $banding = Banding::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.recapture.update', $admission->patient), [
                'recaptured_at' => '2023-06-06',
                'recapture_disposition' => '1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'id' => $banding->id,
            'recaptured_at' => '2023-06-06',
            'recapture_disposition' => '1',
        ]);
    }
}
