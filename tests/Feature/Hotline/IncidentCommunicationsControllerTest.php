<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Models\Communication;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
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

    public function test_un_authenticated_users_cant_access_an_incidents_communications(): void
    {
        $communication = Communication::Factory()->create([
            'incident_id' => Incident::factory()->create(),
        ]);

        $this->get(route('hotline.incident.communications.index', $communication->incident))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_an_incidents_communications(): void
    {
        $me = $this->createTeamUser();
        $communication = Communication::Factory()->create([
            'incident_id' => Incident::factory()->create(['team_id' => $me->team->id]),
        ]);

        $this->actingAs($me->user)->get(route('hotline.incident.communications.index', $communication->incident))->assertForbidden();
    }

    public function test_it_displays_an_incidents_communications(): void
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

    public function test_a_communication_at_date_is_required_to_create_a_communication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident))
            ->assertInvalid(['communication_at' => 'The date responded field is required.']);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident), ['communication_at' => 'foo'])
            ->assertInvalid(['communication_at' => 'The date responded field is not a valid date.']);
    }

    public function test_a_occurred_at_date_is_required_to_create_a_communication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);

        $this->actingAs($me->user)->post(route('hotline.incident.communications.store', $incident))
            ->assertInvalid(['communication' => 'The communication field is required.']);
    }

    public function test_a_new_communication_is_saved_to_storage(): void
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

    public function test_a_communication_at_date_is_required_to_update_a_communication(): void
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

    public function test_a_communication_is_required_to_update_a_communication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->put(route('hotline.incident.communications.update', [$incident, $communication]))
            ->assertInvalid(['communication' => 'The communication field is required.']);
    }

    public function test_it_validates_ownership_of_an_incident_before_displaying_a_communication_to_edit_it(): void
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

    public function test_a_communication_is_updated_in_storage(): void
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

    public function test_it_validates_ownership_of_an_incident_before_deleting_a_communication(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $incident = Incident::factory()->create();
        $communication = Communication::factory()->create(['incident_id' => $incident->id]);

        $this->actingAs($me->user)->delete(route('hotline.incident.communications.update', [$incident, $communication]))
            ->assertOwnershipValidationError();
    }

    public function test_a_communication_can_be_deleted(): void
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
