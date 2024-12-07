<?php

namespace Tests\Feature\Admissions;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class AdmissionsShortcutsTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function anAdmissionForATeamCanBeViewedUsingUrlParameters(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, 2022);

        $this->actingAs($me->user)
            ->get(route('patients.initial.edit', ['c' => 1, 'y' => 2022, 'a' => $me->team->id]))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->where('admission.patient_id', $admission->patient_id)
            );
    }

    #[Test]
    public function anAdmissionForATeamCanNotBeViewedUsingUrlParametersIfTheUserDoesNotBelongToTheTeam(): void
    {
        $me = $this->createTeamUser();
        $this->createCase($me->team, 2022);
        Team::factory()->create(['id' => 9999]);

        $this->actingAs($me->user)
            ->get(route('patients.initial.edit', ['c' => 1, 'y' => 2022, 'a' => 9999]))
            ->assertNotFound();
    }
}
