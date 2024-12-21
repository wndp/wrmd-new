<?php

namespace Tests\Feature\Maintenance;

use App\Models\Admission;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class UncollaborateTransferControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantUncollaborateAPatient(): void
    {
        $transfer = Transfer::factory()->create();
        $this->post(route('maintenance.transfers.uncollaborate', $transfer))->assertRedirect('login');
    }

    #[Test]
    public function aTransferMustBeAcceptedCollaboratedToUncollaboratedIt(): void
    {
        $me = $this->createTeamUser();
        $fromAdmission = $this->createCase($me->team, 2021);
        $toAdmission = Admission::factory()->create(['patient_id' => $fromAdmission->patient_id, 'case_year' => 2021]);
        $transfer = Transfer::factory()->create([
            'patient_id' => $fromAdmission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $toAdmission->team->id,
            'is_collaborative' => true,
            'is_accepted' => 0,
            'responded_at' => Carbon::now(),
        ]);

        $this->actingAs($me->user)
            ->post(route('maintenance.transfers.uncollaborate', $transfer))
            ->assertRedirect()
            ->assertHasNotificationMessage('There was a problem accessing that record. Please try again or contact support for help.');

        $transfer = Transfer::factory()->create([
            'patient_id' => $fromAdmission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $toAdmission->team->id,
            'is_collaborative' => false,
            'is_accepted' => 1,
            'responded_at' => Carbon::now(),
        ]);

        $this->actingAs($me->user)
            ->post(route('maintenance.transfers.uncollaborate', $transfer))
            ->assertRedirect()
            ->assertHasNotificationMessage('There was a problem accessing that record. Please try again or contact support for help.');
    }

    #[Test]
    public function aTransferIsUncollaboratedByTheFromAccount(): void
    {
        $me = $this->createTeamUser();
        $fromAdmission = $this->createCase($me->team, 2021);
        $toAdmission = Admission::factory()->create(['patient_id' => $fromAdmission->patient_id, 'case_year' => 2021]);
        $transfer = Transfer::factory()->create([
            'patient_id' => $fromAdmission->patient_id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $toAdmission->team->id,
            'is_collaborative' => true,
            'is_accepted' => 1,
            'responded_at' => Carbon::now(),
        ]);

        $this->actingAs($me->user)
            ->post(route('maintenance.transfers.uncollaborate', $transfer->id))
            ->assertRedirect(route('patients.initial.edit', ['c' => 1, 'y' => 2021]));

        $this->assertDatabaseHas('admissions', [
            'team_id' => $fromAdmission->team_id,
            'case_year' => $fromAdmission->case_year,
            'case_id' => $fromAdmission->case_id,
            'patient_id' => $toAdmission->patient_id,
        ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $toAdmission->team_id,
            'case_year' => $toAdmission->case_year,
            'case_id' => $toAdmission->case_id,
            'patient_id' => $toAdmission->fresh()->patient_id,
        ]);
    }

    #[Test]
    public function aTransferIsUncollaboratedByTheToAccount(): void
    {
        $me = $this->createTeamUser();
        $you = $this->createTeamUser();
        $fromAdmission = $this->createCase($me->team, 2021);
        $toAdmission = Admission::factory()->create([
            'team_id' => $you->team->id,
            'case_year' => 2021,
            'patient_id' => $fromAdmission->patient_id,
        ]);
        $transfer = Transfer::factory()->create([
            'patient_id' => $fromAdmission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $toAdmission->team->id,
            'is_collaborative' => true,
            'is_accepted' => 1,
            'responded_at' => Carbon::now(),
        ]);

        $this->actingAs($you->user)
            ->post(route('maintenance.transfers.uncollaborate', $transfer->id))
            ->assertRedirect(route('patients.initial.edit', ['c' => 1, 'y' => 2021]));

        $this->assertDatabaseHas('admissions', [
            'team_id' => $fromAdmission->team_id,
            'case_year' => $fromAdmission->case_year,
            'case_id' => $fromAdmission->case_id,
            'patient_id' => $fromAdmission->patient_id,
        ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $toAdmission->team_id,
            'case_year' => $toAdmission->case_year,
            'case_id' => $toAdmission->case_id,
            'patient_id' => $toAdmission->fresh()->patient_id,
        ]);
    }
}
