<?php

namespace Tests\Feature\Patients;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class DetachRescuerControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_it_detatches_a_patient_from_its_rescuer(): void
    {
        $me = $this->createTeamUser();
        $rescuer = Person::factory()->create(['team_id' => $me->team->id, 'first_name' => 'Jimmy']);

        $admission = $this->createCase($me->team, date('Y'), [
            'rescuer_id' => $rescuer->id,
            'common_name' => 'fooSpecies',
        ]);

        $this->actingAs($me->user)
            ->from(route('patients.rescuer.edit'))
            ->delete(route('patients.detach_rescuer.destroy', $admission->patient))
            ->assertRedirect(route('patients.rescuer.edit'));

        $this->assertDatabaseMissing('patients', [
            'rescuer_id' => $rescuer->id,
            'common_name' => 'fooSpecies',
        ]);

        $this->assertDatabaseHas('people', [
            'id' => $rescuer->id,
            'first_name' => 'Jimmy',
        ]);
    }
}
