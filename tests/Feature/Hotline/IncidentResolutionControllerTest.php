<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('hotline')]
final class IncidentResolutionControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_hotline(): void
    {
        $incident = Incident::factory()->create();

        $this->put(route('hotline.incident.update.resolution', $incident))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_hotline(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->for($me->team)->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident))->assertForbidden();
    }

    public function test_it_validates_ownership_of_an_incident_before_updating_it(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident))
            ->assertOwnershipValidationError();
    }

    public function test_an_resolved_at_date_is_required_when_a_resolution_is_present_to_update_an_incident(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->create([
            'team_id' => $me->team->id,
            'occurred_at' => '2022-07-02 17:30:00',
        ]);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolution' => 'foo',
        ])
            ->assertInvalid(['resolved_at' => 'The date resolved field is required when a resolution is given.']);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolution' => 'foo',
            'resolved_at' => 'foo',
        ])
            ->assertInvalid(['resolved_at' => 'The date resolved field is not a valid date.']);

        $this->actingAs($me->user)
            ->put(route('hotline.incident.update.resolution', $incident), [
                'resolution' => 'foo',
                'resolved_at' => '2022-07-01',
            ])
            ->assertInvalid(['resolved_at' => 'The resolved at field must be a date after or equal to 2022-07-02 10:30:00.']);
    }

    public function test_an_incident_is_updated_in_storage(): void
    {
        $hotlineStatusIsResolvedId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED);
        $hotlineStatusIsOpenId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->for($me->team)->create([
            'occurred_at' => '2022-07-02 17:30:00',
        ]);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolved_at' => '2022-07-08 10:20:00',
            'resolution' => 'Pam Beesly picked bird up',
            'given_information' => '1',
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_number' => 'HL-'.date('y').'-0001',
            'resolved_at' => '2022-07-08 17:20:00',
            'resolution' => 'Pam Beesly picked bird up',
            'given_information' => 1,
        ]);
    }

    public function test_saving_a_resolution_date_will_automatically_change_the_incident_status_to_resolved(): void
    {
        $hotlineStatusIsResolvedId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED);
        $hotlineStatusIsOpenId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN);

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->for($me->team)->create([
            'reported_at' => '2022-07-02 15:30:00',
            'occurred_at' => '2022-07-02 17:30:00',
            'incident_status_id' => $hotlineStatusIsOpenId,
        ]);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolved_at' => '2022-07-08 10:20:00',
            'resolution' => 'Pam Beesly picked bird up',
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'resolved_at' => '2022-07-08 17:20:00',
        ]);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolved_at' => null,
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_status_id' => $hotlineStatusIsOpenId,
            'resolved_at' => null,
        ]);
    }
}
