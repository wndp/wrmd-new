<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class CageCardControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_the_cage_card(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.cage_card.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_cage_card(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.cage_card.update', $patient))->assertForbidden();
    }

    public function test_it_fails_validation_when_trying_to_update_the_cage_card(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['dispositioned_at' => null]);
        $admission2 = $this->createCase($me->team, patientOverrides: ['dispositioned_at' => '2022-08-01']);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_CAGE_CARD->value);

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission->patient))
            ->assertInvalid([
                'common_name' => 'The common name field is required.',
                'admitted_at' => 'The date admitted field is required.'
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission->patient), [
                'admitted_at' => 'foo',
            ])
            ->assertInvalid(['admitted_at' => 'The date admitted is not a valid date.']);

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission->patient), [
                'admitted_at' => Carbon::tomorrow(),
            ])
            ->assertInvalid(['admitted_at' => 'The date admitted must be a date before or equal to today.']);

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $admission2->patient), [
                'admitted_at' => Carbon::tomorrow(),
            ])
            ->assertInvalid(['admitted_at' => 'The date admitted must be a date before or equal to 2022-08-01.']);
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_cage_card(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create(['dispositioned_at' => Carbon::tomorrow()]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_CAGE_CARD->value);

        $this->actingAs($me->user)
            ->put(route('patients.cage_card.update', $patient), [
                'admitted_at' => Carbon::today()->format('Y-m-d H:i:s'),
                'common_name' => 'foo',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_updates_the_cage_card(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['dispositioned_at' => null]);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_CAGE_CARD->value);
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
            'date_admitted_at' => $yesterday->setTimezone('UTC')->format('Y-m-d'),
            'time_admitted_at' => $yesterday->setTimezone('UTC')->format('H:i:s'),
            'band' => 'bar',
            'name' => 'Jim',
            'reference_number' => 'PO-123',
            'microchip_number' => 'LE-789',
        ]);
    }
}
