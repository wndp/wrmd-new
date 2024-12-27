<?php

namespace Tests\Feature\Patients\Research;

use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class ResearchControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_access_band_morphometrics(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.research.edit', $patient))->assertRedirect('login');
    }

    public function test_it_displays_the_band_morphometrics_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);

        $this->actingAs($me->user)->get(route('patients.research.edit'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Research/Edit')
                    ->where('admission.patient_id', $admission->patient_id)
            );
    }
}
