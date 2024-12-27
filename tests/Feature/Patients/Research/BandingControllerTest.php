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

final class BandingControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_banding(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.research.banding.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_banding(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.research.banding.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_banding(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.banding.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_banding(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->put(route('patients.research.banding.update', $admission->patient))
            ->assertHasValidationError('band_number', 'The band number field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.research.banding.update', $admission->patient))
            ->assertHasValidationError('banded_at', 'The banding date field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.research.banding.update', $admission->patient), [
                'banded_at' => 'foo',
            ])
            ->assertHasValidationError('banded_at', 'The banding date is not a valid date.');

        $this->actingAs($me->user)
            ->put(route('patients.research.banding.update', $admission->patient), [
                'banded_at' => Carbon::now()->subDays(30),
            ])
            ->assertHasValidationError('banded_at');
    }

    public function test_it_saves_a_new_banding(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2023-06-01']);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.banding.update', $admission->patient), [
                'band_number' => 'abc123',
                'banded_at' => '2023-06-06',
                'age_code' => 'J',
                'how_aged' => 'EY',
                'sex_code' => 'M',
                'how_sexed' => 'CL',
                'status_code' => '7',
                'additional_status_code' => '00',
                'band_size' => '0A',
                'master_bander_id' => 'SOS',
                'banded_by' => 'John Doe',
                'location_id' => 'LB123',
                'band_disposition' => '1',
                'remarks' => 'test',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'patient_id' => $admission->patient_id,
            'band_number' => 'abc123',
            'banded_at' => '2023-06-06',
            'age_code' => 'J',
            'how_aged' => 'EY',
            'sex_code' => 'M',
            'how_sexed' => 'CL',
            'status_code' => '7',
            'additional_status_code' => '00',
            'band_size' => '0A',
            'master_bander_id' => 'SOS',
            'banded_by' => 'John Doe',
            'location_id' => 'LB123',
            'band_disposition' => '1',
            'remarks' => 'test',
        ]);
    }

    public function test_it_updates_an_existing_banding(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2023-06-01']);
        $banding = Banding::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('update-research');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.research.banding.update', $admission->patient), [
                'band_number' => 'abc123',
                'banded_at' => '2023-06-06',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('bandings', [
            'id' => $banding->id,
            'band_number' => 'abc123',
            'banded_at' => '2023-06-06',
        ]);
    }
}
