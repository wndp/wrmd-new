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
final class IncidentPatientControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_a_case_number_is_required_to_relate_a_patient_to_an_incident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident))
            ->assertInvalid(['case_number' => 'The case number field is required.']);
    }

    public function test_the_case_number_must_be_confirmed_to_relate_a_patient_to_an_incident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident), [
            'case_number' => 'x',
        ])
            ->assertInvalid(['case_number' => 'The case number field confirmation does not match.']);
    }

    public function test_it_validates_ownership_of_an_incident_before_relating_it_to_a_patient(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident), [
            'case_number' => '22-123',
            'case_number_confirmation' => '22-123',
        ])
            ->assertOwnershipValidationError();
    }

    public function test_it_validates_ownership_of_the_admission_before_relating_it_to_a_patient(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident), [
            'case_number' => $admission->case_number,
            'case_number_confirmation' => $admission->case_number,
        ])
            ->assertStatus(302)
            ->assertSessionHas(
                'notification.text',
                "A patient with the case number {$admission->case_number} was not found in your account."
            );
    }

    public function test_an_incident_is_related_to_a_patient(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);
        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident), [
            'case_number' => $admission->case_number,
            'case_number_confirmation' => $admission->case_number,
        ])
            ->assertRedirect(route('hotline.incident.edit', $incident));

        $this->assertDatabaseHas('incidents', [
            'id' => $incident->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
