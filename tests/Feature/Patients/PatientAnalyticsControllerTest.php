<?php

namespace Tests\Feature\Patients;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class PatientAnalyticsControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_the_patient_analytics_view(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.analytics', $patient))->assertRedirect('login');
    }

    public function test_it_displays_the_patient_analytics_view(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)->get(route('patients.analytics'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Analytics')
                    ->has('admission')
                    ->where('admission.patient_id', $admission->patient_id)
            );
    }
}
