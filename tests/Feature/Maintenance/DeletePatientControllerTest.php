<?php

namespace Tests\Feature\Maintenance;

use App\Enums\Ability;
use App\Events\AccountUpdated;
use App\Events\PatientDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class DeletePatientControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessDeletePatient(): void
    {
        $this->get(route('patient.delete.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessDeletePatient(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('patient.delete.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheViewToDeleteAPatient(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $response = $this->actingAs($me->user)->get(route('patient.delete.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/DeletePatient')
                    ->has('yearsInAccount');
            });
    }

    #[Test]
    public function aValidCaseYearIsRequiredToDelteAPatient(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $this->actingAs($me->user)->delete(route('patient.delete.destroy'))
            ->assertInvalid(['year' => 'The year field is required.']);

        $this->actingAs($me->user)->delete(route('patient.delete.destroy', ['year' => 1]))
            ->assertInvalid(['year' => 'The selected year is invalid.']);
    }

    #[Test]
    public function theAuthenticatedUsersPasswordIsRequiredToDelteAPatient(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $this->actingAs($me->user)->delete(route('patient.delete.destroy'))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('patient.delete.destroy', ['password' => 'x']))
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);

        $this->actingAs($me->user)->delete(route('patient.delete.destroy', ['password' => 'x', 'password_confirmation' => 'x']))
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    #[Test]
    public function theLastAdmittedPatientCanBeDeleted(): void
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $admission1 = $this->createCase($me->team, 2017);
        $admission2 = $this->createCase($me->team, 2017);

        $this->actingAs($me->user)->delete(route('patient.delete.destroy', [
            'year' => 2017,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertRedirect(route('patient.delete.index'));

        // Event::assertDispatched(AccountUpdated::class, function ($e) use ($me) {
        //     return $e->account->id === $me->team->id;
        // });

        // Event::assertDispatched(PatientDeleted::class, function ($e) use ($admission2) {
        //     return $e->patient->id === $admission2->patient_id;
        // });

        // $this->assertDatabaseHas('metadata', [
        //     'foreign_id' => $me->team->id,
        //     'meta_type' => 'deletion',
        //     'meta_key' => 'deleted_patient',
        //     'meta_value' => '17-2 deleted by '.$me->user->name,
        // ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $me->team->id,
            'case_year' => 2017,
            'case_id' => 1,
        ]);

        $this->assertDatabaseMissing('admissions', [
            'team_id' => $me->team->id,
            'case_year' => 2017,
            'case_id' => 2,
        ]);
    }
}
