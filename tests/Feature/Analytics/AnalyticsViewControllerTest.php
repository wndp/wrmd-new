<?php

namespace Tests\Feature\Analytics;

use App\Enums\SettingKey;
use Carbon\Carbon;
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
        $me = $this->createTeamUser();

        $this->setSetting($me->team, SettingKey::TIMEZONE, 'UTC');

        $this->actingAs($me->user)
            ->get('analytics')
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Analytics/Patients/Overview')
                    ->has(
                        'analytics.filters',
                    )
                    ->where('analytics.filters', function ($filters) {
                        return $filters['segments'][0] === 'All Patients'
                            && $filters['date_period'] === 'past-7-days'
                            && $filters['date_from'] === Carbon::now('UTC')->subDays(6)->format('Y-m-d')
                            && $filters['date_to'] === Carbon::now('UTC')->format('Y-m-d');
                    });
            });
    }
}
