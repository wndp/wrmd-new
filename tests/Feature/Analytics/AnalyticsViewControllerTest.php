<?php

namespace Tests\Feature\Analytics;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

final class AnalyticsViewControllerTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itDisplaysTheAnalyticsIndexView(): void
    {
        $this->actingAs($this->createTeamUser()->user)
            ->get('analytics')
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Analytics/Patients/Overview')
                    ->hasAll([
                        'analytics.filters',
                    ])
                    ->where('analytics.filters', function ($filters) {
                        return $filters['segments'][0] === 'All Patients'
                            && $filters['date_period'] === 'past-7-days'
                            && $filters['date_from'] === now()->subDays(6)->format('Y-m-d')
                            && $filters['date_to'] === now()->format('Y-m-d');
                    });
            });
    }
}
