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
final class IncidentPatientControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function aCaseNumberIsRequiredToRelateAPatientToAnIncident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident))
            ->assertInvalid(['case_number' => 'The case number field is required.']);
    }

    #[Test]
    public function theCaseNumberMustBeConfirmedToRelateAPatientToAnIncident(): void
    {
        $me = $this->createTeamUser();
        $incident = Incident::factory()->create(['team_id' => $me->team->id]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_HOTLINE->value);

        $this->actingAs($me->user)->post(route('hotline.incident.patient.store', $incident), [
            'case_number' => 'x',
        ])
            ->assertInvalid(['case_number' => 'The case number field confirmation does not match.']);
    }

    #[Test]
    public function itValidatesOwnershipOfAnIncidentBeforeRelatingItToAPatient(): void
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

    #[Test]
    public function itValidatesOwnershipOfTheAdmissionBeforeRelatingItToAPatient(): void
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

    #[Test]
    public function anIncidentIsRelatedToAPatient(): void
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
