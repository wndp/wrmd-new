<?php

namespace Tests\Feature\Sharing;

use App\Enums\Ability;
use App\Models\Admission;
use App\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class TransferResponseControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_only_the_recipient_account_can_accept_a_transfer_request(): void
    {
        $you = $this->createTeamUser();
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $transfer = Transfer::factory()->create([
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $you->team->id,
        ]);
        BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($me->user)
            ->post(route('share.transfer.accept', $transfer->id))
            ->assertRedirect()
            ->assertHasNotificationMessage('Your team can not accept this transfer request.');
    }

    public function test_a_transfer_request_is_accepted(): void
    {
        $me = $this->createTeamUser();
        $you = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $transfer = Transfer::factory()->create([
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $you->team->id,
        ]);
        BouncerFacade::allow($you->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($you->user)
            ->post(route('share.transfer.accept', $transfer->id))
            ->assertRedirect(route('patients.initial.edit', ['c' => 1, 'y' => date('Y')]));

        $newPatient = Admission::where('team_id', $you->team->id)->first()->patient;

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'is_accepted' => 1,
            'patient_id' => $admission->patient->id,
            'cloned_patient_id' => $newPatient->id,
        ]);

        $this->assertEquals($you->team->id, $newPatient->team_possession_id);
    }

    public function test_transfers_can_not_be_accepted_if_they_have_already_been_responded_to(): void
    {
        $me = $this->createTeamUser();
        $you = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $transfer = Transfer::factory()->create([
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $you->team->id,
        ]);
        BouncerFacade::allow($you->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($you->user)
            ->post(route('share.transfer.accept', $transfer->id));

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'patient_id' => $admission->patient->id,
            'is_accepted' => 1,
        ]);

        $this->actingAs($you->user)
            ->post(route('share.transfer.accept', $transfer->id))
            ->assertRedirect()
            ->assertHasNotificationMessage('This transfer request has already be responded to.');
    }

    public function test_a_collaborative_transfer_request_is_accepted(): void
    {
        $me = $this->createTeamUser();
        $you = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $transfer = Transfer::factory()->create([
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $you->team->id,
            'is_collaborative' => true,
        ]);
        BouncerFacade::allow($you->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($you->user)
            ->post(route('share.transfer.accept', $transfer->id))
            ->assertRedirect(route('patients.initial.edit', ['c' => 1, 'y' => date('Y')]));

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'is_accepted' => 1,
            'patient_id' => $admission->patient->id,
            'cloned_patient_id' => null,
        ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $you->team->id,
            'patient_id' => $admission->patient->id,
        ]);

        $this->assertEquals($you->team->id, $admission->patient->refresh()->team_possession_id);
    }

    public function test_accounts_can_not_accept_a_collaborative_patient_they_already_have_admitted(): void
    {
        $me = $this->createTeamUser();
        $you = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $transfer = Transfer::factory()->create([
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $you->team->id,
            'is_collaborative' => true,
        ]);
        BouncerFacade::allow($you->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($you->user)
            ->post(route('share.transfer.accept', $transfer->id));

        $newTransfer = Transfer::factory()->create([
            'patient_id' => $admission->patient->id,
            'from_team_id' => $me->team->id,
            'to_team_id' => $you->team->id,
            'is_collaborative' => true,
        ]);

        $this->actingAs($you->user)
            ->post(route('share.transfer.accept', $newTransfer->id))
            ->assertHasNotificationMessage('This patient has already been admitted to your team.');
    }

    public function test_a_transfer_request_is_denied(): void
    {
        $me = $this->createTeamUser();
        $transfer = Transfer::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($me->user)
            ->delete(route('share.transfer.deny', $transfer->id))
            ->assertRedirect(route('maintenance.transfers'));

        $this->assertDatabaseHas('transfers', [
            'id' => $transfer->id,
            'is_accepted' => 0,
        ]);
    }
}
