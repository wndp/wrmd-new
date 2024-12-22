<?php

namespace Tests\Feature\Admissions;

use App\Exceptions\AdmissionNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class AdmissionNotFoundTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_redirect_when_admission_not_found(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(AdmissionNotFoundException::class);

        $me = $this->createTeamUser();

        $this->actingAs($me->user)
            ->get('patients/initial?y=2022&c=99')
            ->assertInertia(
                fn ($page) => $page->component('Admissions/404')
            );
    }
}
