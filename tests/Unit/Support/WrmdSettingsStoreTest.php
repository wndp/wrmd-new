<?php

namespace Tests\Unit\Support;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Repositories\SettingsStore;
use App\Support\Wrmd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WrmdSettingsStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app()->singleton(SettingsStore::class, function () {
            $setting = Setting::factory()->create([
                'key' => SettingKey::TIMEZONE->value,
                'value' => 'bar',
            ]);

            return new SettingsStore($setting->team);
        });
    }

    #[Test]
    public function itGetsTheSettingsStore(): void
    {
        $result = Wrmd::settings();

        $this->assertInstanceOf(SettingsStore::class, $result);
    }

    #[Test]
    public function itGetsAnAccountsSetting(): void
    {
        $result = Wrmd::settings(SettingKey::TIMEZONE);

        $this->assertEquals('bar', $result);
    }

    #[Test]
    public function itGetsADefaultSetting(): void
    {
        $result = Wrmd::settings(SettingKey::LANGUAGE, 'Yes');

        $this->assertEquals('Yes', $result);
    }

    #[Test]
    public function itSetsAnAccountsSetting(): void
    {
        Wrmd::settings([SettingKey::LANGUAGE->value => 'bang']);

        $result = Wrmd::settings(SettingKey::LANGUAGE);

        $this->assertEquals('bang', $result);
    }
}
