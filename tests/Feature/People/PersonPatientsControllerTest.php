<?php

namespace Tests\Feature\People;

use App\Enums\Role;
use App\Models\Person;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PersonPatientsControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessAPersonsDonations(): void
    {
        $team = Team::factory()->create();
        $rescuer = Person::factory()->create(['team_id' => $team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $this->createCase($team, patientOverrides: ['date_admitted_at' => now(), 'rescuer_id' => $rescuer->id]);

        $this->get(route('people.patients.index', $rescuer))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessPeople(): void
    {
        $me = $this->createTeamUser(role: Role::USER);
        $rescuer = Person::factory()->create(['team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $this->createCase($me->team, patientOverrides: ['date_admitted_at' => now(), 'rescuer_id' => $rescuer->id]);

        $this->actingAs($me->user)->get(route('people.patients.index', $rescuer))->assertForbidden();
    }

    #[Test]
    public function itValidatesOwnershipOfAPersonBeforeDisplayingThierPatients(): void
    {
        $me = $this->createTeamUser();
        $team = Team::factory()->create();
        $rescuer = Person::factory()->create(['team_id' => $team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $this->createCase($team, patientOverrides: ['date_admitted_at' => now(), 'rescuer_id' => $rescuer->id]);

        $this->actingAs($me->user)->get(route('people.patients.index', $rescuer))
            ->assertOwnershipValidationError();
    }

    #[Test]
    public function itDisplaysPersonsPatients(): void
    {
        $me = $this->createTeamUser();
        $rescuer = Person::factory()->create(['team_id' => $me->team->id, 'first_name' => 'John', 'last_name' => 'Doe']);
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => now(), 'rescuer_id' => $rescuer->id]);

        $this->actingAs($me->user)->get(route('people.patients.index', $rescuer))
            ->assertOk()
            ->assertInertia(function ($page) use ($admission) {
                $page->component('People/Patients')
                    ->has('person')
                    ->has('listFigures')
                    ->where('listFigures.rows.data.0.patient_id', $admission->patient_id);
            });
    }
}
