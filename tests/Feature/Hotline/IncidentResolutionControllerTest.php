<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function unAuthenticatedUsersCantAccessHotline(): void
    {
        $incident = Incident::factory()->create();

        $this->put(route('hotline.incident.update.resolution', $incident))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessHotline(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->for($me->team)->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeUpdatingIt(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function anResolvedAtDateIsRequiredWhenAResolutionIsPresentToUpdateAnIncident(): void
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

    #[Test]
    public function anIncidentIsUpdatedInStorage(): void
    {
        $hotlineStatusIsResolvedId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED)
            ->attribute_option_id;

        $hotlineStatusIsOpenId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN)
            ->attribute_option_id;

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

    #[Test]
    public function savingAResolutionDateWillAutomaticallyChangeTheIncidentStatusToResolved(): void
    {
        $hotlineStatusIsResolvedId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED)
            ->attribute_option_id;

        $hotlineStatusIsOpenId = $this->createUiBehavior(AttributeOptionName::HOTLINE_STATUSES, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN)
            ->attribute_option_id;

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->for($me->team)->create([
            'occurred_at' => '2022-07-02 17:30:00',
            'incident_status_id' => $hotlineStatusIsOpenId
        ]);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolved_at' => '2022-07-08 10:20:00',
            'resolution' => 'Pam Beesly picked bird up',
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_number' => 'hl-'.date('y').'-0001',
            'incident_status_id' => $hotlineStatusIsResolvedId,
            'resolved_at' => '2022-07-08 17:20:00',
        ]);

        $this->actingAs($me->user)->put(route('hotline.incident.update.resolution', $incident), [
            'resolved_at' => null,
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_number' => 'hl-'.date('y').'-0001',
            'incident_status_id' => $hotlineStatusIsOpenId,
            'resolved_at' => null,
        ]);
    }
}
