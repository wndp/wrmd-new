<?php

namespace Tests\Feature\Admissions;

use App\Exceptions\AdmissionNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

final class AdmissionNotFoundTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function redirectWhenAdmissionNotFound(): void
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
