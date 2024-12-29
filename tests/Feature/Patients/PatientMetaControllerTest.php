<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientMetaControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_update_the_meta(): void
    {
        $patient = Patient::factory()->create();
        $this->put(route('patients.meta.update', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_the_meta(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->put(route('patients.meta.update', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_updating_the_meta(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_META->value);

        $this->actingAs($me->user)
            ->put(route('patients.meta.update', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_meta(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_META->value);

        $this->actingAs($me->user)
            ->put(route('patients.meta.update', $admission->patient))
            ->assertInvalid([
                'is_locked' => 'The is locked field is required.',
                'is_voided' => 'The is voided field is required.',
                'is_criminal_activity' => 'The criminal activity field is required.'
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.meta.update', $admission->patient), [
                'is_locked' => 'foo',
                'is_voided' => 'foo',
                'is_criminal_activity' => 'foo',
                //'keywords' => 1,
            ])
            ->assertInvalid([
                'is_locked' => 'The is locked field must be true or false.',
                'is_voided' => 'The is voided field must be true or false.',
                'is_criminal_activity' => 'The criminal activity field must be true or false.',
                //'keywords' => 'The keywords must be a string.'
            ]);
    }

    public function test_it_updates_the_meta(): void
    {
        $this->freezeTime();

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::UPDATE_PATIENT_META->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.meta.update', $admission->patient), [
                'is_locked' => 0,
                'is_voided' => 1,
                'is_resident' => 1,
                'is_criminal_activity' => 0,
                //'keywords' => 'test',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('patients', [
            'id' => $admission->patient_id,
            'locked_at' => null,
            'voided_at' => Carbon::now(),
            'is_resident' => true,
            'is_criminal_activity' => false,
            //'keywords' => 'test',
        ]);
    }
}
