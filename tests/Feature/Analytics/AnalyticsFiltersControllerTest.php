<?php

namespace Tests\Feature\Analytics;

use App\Analytics\AnalyticFiltersStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AnalyticsFiltersControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itUpdatesTheFiltersToGenerateAnalyticsWith(): void
    {
        AnalyticFiltersStore::destroy();

        $this->assertEmpty(AnalyticFiltersStore::all());

        $filters = [
            'segments' => ['foo'],
            'date_period' => 'this-month',
            'date_from' => '2020-06-01',
            'date_to' => '2020-06-30',
            'compare' => true,
            'compare_period' => 'previousperiod',
            'compare_date_from' => '2020-05-01',
            'compare_date_to' => '2020-05-31',
            'group_by_period' => 'month',
            'limit_to_search' => false,
        ];

        $this->actingAs($this->createTeamUser()->user)
            ->put('analytics/filters', $filters)
            ->assertRedirect();

        $this->assertSame(AnalyticFiltersStore::all()->toArray(), $filters);

        AnalyticFiltersStore::destroy();
    }
}
