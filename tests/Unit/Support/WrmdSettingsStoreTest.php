<?php

namespace Tests\Unit\Support;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Repositories\SettingsStore;
use App\Support\Wrmd;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_it_gets_the_settings_store(): void
    {
        $result = Wrmd::settings();

        $this->assertInstanceOf(SettingsStore::class, $result);
    }

    public function test_it_gets_an_accounts_setting(): void
    {
        $result = Wrmd::settings(SettingKey::TIMEZONE);

        $this->assertEquals('bar', $result);
    }

    public function test_it_gets_a_default_setting(): void
    {
        $result = Wrmd::settings(SettingKey::LANGUAGE, 'Yes');

        $this->assertEquals('Yes', $result);
    }

    public function test_it_sets_an_accounts_setting(): void
    {
        Wrmd::settings([SettingKey::LANGUAGE->value => 'bang']);

        $result = Wrmd::settings(SettingKey::LANGUAGE);

        $this->assertEquals('bang', $result);
    }
}
