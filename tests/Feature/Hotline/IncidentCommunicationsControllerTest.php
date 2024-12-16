<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Models\Communication;
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
final class IncidentCommunicationsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAnIncidentsCommunications(): void
    {
        $communication = Communication::Factory()->create([
            'incident_id' => Incident::factory()->create(),
        ]);

        $this->get(route('hotline.incident.communications.index', $communication->incident))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessAnIncidentsCommunications(): void
    {
        $me = $this->createTeamUser();
        $communication = Communication::Factory()->create([
            'incident_id' => Incident::factory()->create(['team_id' => $me->team->id]),
        ]);

        $this->actingAs($me->user)->get(route('hotline.incident.communications.index', $communication->incident))->assertForbidden();
    }

    #[Test]
    public function itDisplaysAnIncidentsCommunications(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->get(route('hotline.incident.communications.index', $incident))
            ->assertOk()
            ->assertInertia(function ($page) use ($incident) {
                $page->component('Hotline/Communications')
                    ->hasAll('incident', 'communications')
                    ->where('communications.0.id', $incident->communications->first()->id);
            });
    }

    #[Test]
    public function aCommunicationAtDateIsRequiredToCreateACommunication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident))
            ->assertInvalid(['communication_at' => 'The date responded field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident), ['communication_at' => 'foo'])
            ->assertInvalid(['communication_at' => 'The date responded field is not a valid date.']);
    }

    #[Test]
    public function aOccurredAtDateIsRequiredToCreateACommunication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident))
            ->assertInvalid(['communication' => 'The communication field is required.']);
    }

    #[Test]
    public function aNewCommunicationIsSavedToStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident), [
            'communication_at' => '2018-02-14 16:48:00',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
            'communication_by' => 'Michael Scott',
        ])
            ->assertredirect(route('hotline.incident.communications.index', $incident));

        $this->assertDatabaseHas('communications', [
            'incident_id' => $incident->id,
            'communication_at' => '2018-02-15 00:48:00',
            'communication_by' => 'Michael Scott',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
        ]);
    }

    #[Test]
    public function aCommunicationAtDateIsRequiredToUpdateACommunication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.communications.update', [$incident, $communication]))
            ->assertInvalid(['communication_at' => 'The date responded field is required.']);

        $this->actingAs($me->user)->put(route('hotline.incident.communications.update', [$incident, $communication]), ['communication_at' => 'foo'])
            ->assertInvalid(['communication_at' => 'The date responded field is not a valid date.']);
    }

    #[Test]
    public function aCommunicationIsRequiredToUpdateACommunication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.communications.update', [$incident, $communication]))
            ->assertInvalid(['communication' => 'The communication field is required.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeDisplayingACommunicationToEditIt(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create();
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.communications.update', [$incident, $communication]), [
            'communication_at' => '2018-02-14 16:48:00',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
            'communication_by' => 'Michael Scott',
        ])
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aCommunicationIsUpdatedInStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.communications.update', [$incident, $communication]), [
            'communication_at' => '2018-02-14 16:48:00',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
            'communication_by' => 'Michael Scott',
        ])
            ->assertredirect(route('hotline.incident.communications.index', $incident));

        $this->assertDatabaseHas('communications', [
            'incident_id' => $incident->id,
            'communication_at' => '2018-02-15 00:48:00',
            'communication_by' => 'Michael Scott',
            'communication' => 'Pam Beesly will pick bird up from Jim.',
        ]);
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeDeletingACommunication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create();
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->delete(route('hotline.incident.communications.update', [$incident, $communication]))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function aCommunicationCanBeDeleted(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->delete(route('hotline.incident.communications.update', [$incident, $communication]))
            ->assertredirect(route('hotline.incident.communications.index', $incident));

        $this->assertSoftDeleted('communications', [
            'id' => $communication->id,
        ]);
    }
}
