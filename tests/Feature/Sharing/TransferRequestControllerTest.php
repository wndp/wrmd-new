<?php

namespace Tests\Feature\Sharing;

use App\Enums\Ability;
use App\Jobs\SendTransferRequest;
use App\Models\Patient;
use App\Models\Team;
use App\ValueObjects\SingleStorePoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class TransferRequestControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_transfer_request(): void
    {
        $this->get('patients/transfer')->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_transfer_request(): void
    {
        $me = $this->createTeamUser();

        $this->actingAs($me->user)->get('patients/transfer')->assertForbidden();
    }

    public function test_it_displays_the_transfer_request_create_page(): void
    {
        $me = $this->createTeamUser(['name' => 'my account', 'coordinates' => new SingleStorePoint(38.75240451, -122.61498527)]);
        $near = Team::factory()->create(['name' => 'near account', 'coordinates' => new SingleStorePoint(38.9104546, -122.6102614)]);
        $far = Team::factory()->create(['name' => 'far account', 'coordinates' => new SingleStorePoint(40.7142691, -74.0059729)]);
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($me->user)->get(route('share.transfer.create'))
            ->assertOk()
            ->assertInertia(function ($page) use ($admission, $near, $far) {
                $page->component('Patients/Transfer')
                    ->has('teams')
                    ->where('admission.patient_id', $admission->patient_id)
                    ->where('teams.0.group.0.value', $near->id)
                    ->where('teams.1.group.0.value', $far->id);
            });
    }

    public function test_it_validates_ownership_of_the_patient_before_creating_a_transfer_request(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($me->user)
            ->post(route('share.transfer.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_create_a_transfer_request(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($me->user)
            ->post(route('share.transfer.store', $admission->patient))
            ->assertInvalid(['transferTo' => 'The transfer to field is required.']);

        $this->actingAs($me->user)
            ->post(route('share.transfer.store', $admission->patient), [
                'transferTo' => 'foo',
            ])
            ->assertInvalid(['transferTo' => 'The selected organization is unknown.']);
    }

    public function test_a_transfer_request_is_created(): void
    {
        Bus::fake();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $team = Team::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::SHARE_PATIENTS->value);

        $this->actingAs($me->user)
            ->post(route('share.transfer.store', $admission->patient), [
                'transferTo' => $team->id,
                'collaborate' => 1,
            ])
            ->assertRedirect(route('patients.continued.edit', ['y' => $admission->case_year, 'c' => $admission->case_id]));

        Bus::assertDispatched(SendTransferRequest::class, function ($job) use ($me, $team, $admission) {
            return $job->fromTeam->is($me->team) &&
                $job->toTeam->is($team) &&
                $job->patient->is($admission->patient) &&
                $job->isCollaborative === true;
        });
    }
}
