<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Carbon\Carbon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class CageCardControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_update_the_cage_card(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.cage_card.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_cage_card(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.cage_card.update', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_update_the_cage_card(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['dispositioned_at' => null]);
        $admission2 = $this->createCase(['account_id' => $me->account->id], ['dispositioned_at' => '2022-08-01']);
        BouncerFacade::allow($me->user)->to('update-cage-card');

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission->patient))
            ->assertHasValidationError('common_name', 'The common name field is required.')
            ->assertHasValidationError('admitted_at', 'The date admitted field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission->patient), [
                'admitted_at' => 'foo',
            ])
            ->assertHasValidationError('admitted_at', 'The date admitted is not a valid date.');

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission->patient), [
                'admitted_at' => Carbon::tomorrow(),
            ])
            ->assertHasValidationError('admitted_at', 'The date admitted must be a date before or equal to today.');

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission2->patient), [
                'admitted_at' => Carbon::tomorrow(),
            ])
            ->assertHasValidationError('admitted_at', 'The date admitted must be a date before or equal to 2022-08-01.');
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_cage_card(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create(['dispositioned_at' => Carbon::tomorrow()]);
        BouncerFacade::allow($me->user)->to('update-cage-card');

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $patient), [
                'admitted_at' => Carbon::today()->format('Y-m-d H:i:s'),
                'common_name' => 'foo',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_updates_the_cage_card(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['dispositioned_at' => null]);
        BouncerFacade::allow($me->user)->to('update-cage-card');
        $yesterday = Carbon::yesterday('America/Los_Angeles');

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.cage_card.update', $admission->patient), [
                'common_name' => 'foo',
                'admitted_at' => $yesterday->format('Y-m-d H:i:s'),
                'band' => 'bar',
                'name' => 'Jim',
                'reference_number' => 'PO-123',
                'microchip_number' => 'LE-789',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'common_name' => 'foo',
            'admitted_at' => $yesterday->setTimezone('UTC'),
            'band' => 'bar',
            'name' => 'Jim',
            'reference_number' => 'PO-123',
            'microchip_number' => 'LE-789',
        ]);
    }
}
