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
final class IncidentDescriptionControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessHotline(): void
    {
        $incident = Incident::factory()->create();

        $this->put(route('hotline.incident.update.description', $incident))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessHotline(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->for($me->team)->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.description', $incident))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeUpdatingIt(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.description', $incident))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function anIncidentIsUpdatedInStorage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $incident = Incident::factory()->for($me->team)->create();

        $this->actingAs($me->user)->put(route('hotline.incident.update.description', $incident), [
            'incident_address' => '123 Main st.',
            'incident_city' => 'Any town',
            'incident_subdivision' => 'CA',
            'incident_postal_code' => '12345',
            'description' => 'Cat caught bird, unable to bring to hospital',
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'incident_number' => 'hl-'.date('y').'-0001',
            'incident_address' => '123 Main st.',
            'incident_city' => 'Any town',
            'incident_subdivision' => 'CA',
            'incident_postal_code' => '12345',
            'description' => 'Cat caught bird, unable to bring to hospital',
        ]);
    }
}
