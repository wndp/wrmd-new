<?php

namespace Tests\Feature\Patients\BandingAndMorphometrics;

use App\Enums\Extension;
use App\Models\Patient;
use App\Support\ExtensionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class BandingAndMorphometricsControllerTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_banding_and_morphometrics(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.banding-morphometrics.edit', $patient))->assertRedirect('login');
    }

    public function test_if_the_banding_and_morphometrics_extension_is_not_active_the_page_wont_load()
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();

        $this->actingAs($me->user)->get(route('patients.banding-morphometrics.edit', $patient))->assertForbidden();
    }

    public function test_it_displays_the_banding_and_morphometrics_view(): void
    {
        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::BANDING_MORPHOMETRICS);

        $admission = $this->createCase($me->team);

        $this->actingAs($me->user)->get(route('patients.banding-morphometrics.edit'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/BandingMorphometrics/Edit')
                    ->where('admission.patient_id', $admission->patient_id)
                    ->where('patient.id', $admission->patient_id)
            );
    }
}
