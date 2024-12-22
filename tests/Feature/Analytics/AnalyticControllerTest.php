<?php

namespace Tests\Feature\Analytics;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AnalyticControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_an_anylitic_number_response_has_a_defined_json_structure(): void
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

    public function test_an_anylitic_chart_response_has_a_defined_json_structure(): void
    {
        $this->actingAs($this->createTeamUser()->user)
            ->get('analytics/charts/patients-admitted')
            ->assertOk()
            ->assertJsonStructure([
                'categories',
                'series',
            ]);
    }

    public function test_an_anylitic_map_response_has_a_defined_json_structure(): void
    {
        $this->actingAs($this->createTeamUser()->user)
            ->get('analytics/maps/acquisition-location')
            ->assertOk()
            ->assertJsonStructure([
                'series',
            ]);
    }
}
