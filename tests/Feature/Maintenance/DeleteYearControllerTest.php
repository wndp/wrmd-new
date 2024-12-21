<?php

namespace Tests\Feature\Maintenance;

use App\Enums\Ability;
use App\Events\AccountUpdated;
use App\Events\PatientDeleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class DeleteYearControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessDeleteYear(): void
    {
        $this->get(route('year.delete.index'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessDeleteYear(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('year.delete.index'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheViewToDeleteAYearOfPatients(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $response = $this->actingAs($me->user)->get(route('year.delete.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/DeleteYear')
                    ->has('yearsInAccount');
            });
    }

    #[Test]
    public function aValidCaseYearIsRequiredToDeleteAYearOfPatients(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $this->actingAs($me->user)->delete(route('year.delete.destroy'))
            ->assertInvalid(['year' => 'The year field is required.']);

        $this->actingAs($me->user)->delete(route('year.delete.destroy', ['year' => 1]))
            ->assertInvalid(['year' => 'The selected year is invalid.']);
    }

    #[Test]
    public function theAuthenticatedUsersPasswordIsRequiredToDeleteAYearOfPatients(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $this->actingAs($me->user)->delete(route('year.delete.destroy'))
            ->assertInvalid(['password' => 'The password field is required.']);

        $this->actingAs($me->user)->delete(route('year.delete.destroy', ['password' => 'x']))
            ->assertInvalid(['password' => 'The password field confirmation does not match.']);

        $this->actingAs($me->user)->delete(route('year.delete.destroy', ['password' => 'x', 'password_confirmation' => 'x']))
            ->assertInvalid(['password' => 'The password is incorrect.']);
    }

    #[Test]
    public function aYearOfPatientsCanBeDeleted(): void
    {
        Event::fake();

        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_DANGER_ZONE->value);

        $admission1 = $this->createCase($me->team, 2017);
        $admission2 = $this->createCase($me->team, 2018);

        $this->actingAs($me->user)->delete(route('year.delete.destroy', [
            'year' => 2017,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]))
            ->assertRedirect(route('year.delete.index'));

        // Event::assertDispatched(AccountUpdated::class, function ($e) use ($me) {
        //     return $e->account->id === $me->team->id;
        // });

        // Event::assertDispatched(PatientDeleted::class, function ($e) use ($admission1) {
        //     return $e->patient->id === $admission1->patient_id;
        // });

        // $this->assertDatabaseHas('metadata', [
        //     'foreign_id' => $me->team->id,
        //     'meta_type' => 'deletion',
        //     'meta_key' => 'deleted_year',
        //     'meta_value' => '2017 deleted by '.$me->user->name,
        // ]);

        $this->assertDatabaseHas('admissions', [
            'team_id' => $me->team->id,
            'case_year' => 2018,
            'case_id' => 1,
        ]);

        $this->assertDatabaseMissing('admissions', [
            'team_id' => $me->team->id,
            'case_year' => 2017,
            'case_id' => 1,
        ]);
    }
}
