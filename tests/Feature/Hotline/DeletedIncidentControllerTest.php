<?php

namespace Tests\Feature\Hotline;

use App\Enums\Ability;
use App\Models\Incident;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
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

    public function test_an_incident_number_is_required_to_restore_an_incident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident))
            ->assertInvalid(['incident_number' => 'The incident number field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident), ['incident_number' => 'foo'])
            ->assertInvalid(['incident_number' => 'The provided incident number does not match the displayed incident number.']);
    }

    public function test_the_authenticated_users_password_is_required_to_restore_an_incident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('hotline.deleted.destroy', $incident), ['password' => 'foo'])
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    public function test_it_validates_ownership_of_an_incident_before_restore_it(): void
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

    public function test_an_incident_can_be_restored(): void
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
