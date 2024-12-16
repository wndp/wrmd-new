<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

#[Group('hotline')]
final class DeletedIncidentControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function anIncidentNumberIsRequiredToRestoreAnIncident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident))
            ->assertInvalid(['incident_number' => 'The incident number field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident), ['incident_number' => 'foo'])
            ->assertInvalid(['incident_number' => 'The provided incident number does not match the displayed incident number.']);
    }

    #[Test]
    public function theAuthenticatedUsersPasswordIsRequiredToRestoreAnIncident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident), ['password' => 'foo'])
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeRestoreIt(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident), [
            'incident_number' => $incident->incident_number,
            'password' => 'password',
        ])
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function anIncidentCanBeRestored(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident), [
            'incident_number' => $incident->incident_number,
            'password' => 'password',
        ])
            ->assertRedirect(route('hotline.open.index'));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'deleted_at' => null,
        ]);
    }
}
