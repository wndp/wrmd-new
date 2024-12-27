<?php

namespace Tests\Feature\Patients;

use App\Domain\People\Person;
use App\Domain\Taxonomy\Taxon;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class DetachRescuerControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    public function test_it_detatches_a_patient_from_its_rescuer(): void
    {
        Taxon::factory()->unidentified()->create();
        $me = $this->createAccountUser();
        $rescuer = Person::factory()->create(['account_id' => $me->account->id, 'first_name' => 'Jimmy']);

        $admission = $this->createCase(['account_id' => $me->account->id, 'case_year' => date('Y')], [
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
