<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class SettingTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_setting_belongs_to_a_team(): void
    {
        $setting = Setting::factory()->create();

        $this->assertInstanceOf(Team::class, $setting->team);
    }

    public function test_a_setting_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Setting::factory()->create(),
            'created'
        );
    }
}
