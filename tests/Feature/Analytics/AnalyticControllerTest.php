<?php

namespace Tests\Feature\Analytics;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AnalyticControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function anAnyliticNumberResponseHasADefinedJsonStructure(): void
    {
        $this->actingAs($this->createTeamUser()->user)
            ->get('analytics/numbers/patients-admitted')
            ->assertOk()
            ->assertJsonStructure([
                'difference',
                'change',
                'now',
                'prev',
            ]);
    }

    #[Test]
    public function anAnyliticChartResponseHasADefinedJsonStructure(): void
    {
        $this->actingAs($this->createTeamUser()->user)
            ->get('analytics/charts/patients-admitted')
            ->assertOk()
            ->assertJsonStructure([
                'categories',
                'series',
            ]);
    }

    #[Test]
    public function anAnyliticMapResponseHasADefinedJsonStructure(): void
    {
        $this->actingAs($this->createTeamUser()->user)
            ->get('analytics/maps/acquisition-location')
            ->assertOk()
            ->assertJsonStructure([
                'series',
            ]);
    }
}
