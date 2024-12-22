<?php

namespace Tests\Feature\Maintenance;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class UnrecognizedPatientsControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessUnrecognizedPatients(): void
    {
        $this->get(route('maintenance.unrecognized-patients'))->assertRedirect('login');
    }

    #[Test]
    public function unAuthorizedUsersCantAccessUnrecognizedPatients(): void
    {
        $me = $this->createTeamUser();
        $this->actingAs($me->user)->get(route('maintenance.unrecognized-patients'))->assertForbidden();
    }

    #[Test]
    public function itDisplaysTheUnrecognizedPatientsPage(): void
    {
        $me = $this->createTeamUser();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_ACCOUNT_MAINTENANCE->value);

        $unrecognizedAdmission = $this->createCase($me->team, patientOverrides: ['taxon_id' => null]);

        $this->actingAs($me->user)->get(route('maintenance.unrecognized-patients'))
            ->assertOk()
            ->assertInertia(function ($page) use ($unrecognizedAdmission) {
                $page->component('Maintenance/UnrecognizedPatients')
                    ->where('admissions.data.0.patient_id', $unrecognizedAdmission->patient_id);
            });
    }
}
