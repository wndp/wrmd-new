<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class SettingTest extends TestCase
{
    use RefreshDatabase;
    use Assertions;

    #[Test]
    public function aSettingBelongsToATeam(): void
    {
        $setting = Setting::factory()->create();

        $this->assertInstanceOf(Team::class, $setting->team);
    }

    #[Test]
    public function aSettingIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Setting::factory()->create(),
            'created'
        );
    }
}
