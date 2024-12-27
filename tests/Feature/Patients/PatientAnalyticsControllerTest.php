<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class PatientAnalyticsControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_access_the_patient_analytics_view(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.analytics', $patient))->assertRedirect('login');
    }

    public function test_it_displays_the_patient_analytics_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);

        $this->actingAs($me->user)->get(route('patients.analytics'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Analytics')
                    ->has('admission')
                    ->where('admission.patient_id', $admission->patient_id)
            );
    }
}
