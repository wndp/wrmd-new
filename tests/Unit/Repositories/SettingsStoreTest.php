<?php

namespace Tests\Unit\Repositories;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Repositories\SettingsStore;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SettingsStoreTest extends TestCase
{
    use RefreshDatabase;

    protected $team;

    protected $settings;

    protected function setUp(): void
    {
        parent::setUp();

        $setting = Setting::factory()->create([
            'key' => SettingKey::CLINIC_IP->value,
            'value' => 'bar',
        ]);

        $this->team = $setting->team;
        $this->settings = new SettingsStore($setting->team);
    }

    #[Test]
    public function itCantDeleteAProtectedSetting(): void
    {
        $this->settings->set(SettingKey::LANGUAGE, 'en');
        $this->assertFalse($this->settings->delete(SettingKey::LANGUAGE));
    }

    #[Test]
    public function itDeletesAnExistingSetting(): void
    {
        $this->settings->set(SettingKey::CLINIC_IP, 'fun');
        $this->assertTrue($this->settings->delete(SettingKey::CLINIC_IP));
    }

    #[Test]
    public function itDetectsIfASettingExists(): void
    {
        $result = $this->settings->has(SettingKey::CLINIC_IP);
        $this->assertTrue($result);

        $result = $this->settings->has(SettingKey::LOG_ORDER);
        $this->assertFalse($result);
    }

    #[Test]
    public function itGetsADefaultWhenASettingDoesNotExist(): void
    {
        $result = $this->settings->get(SettingKey::LOG_ORDER, 'default');

        $this->assertEquals('default', $result);
    }

    #[Test]
    public function itGetsASetting(): void
    {
        $result = $this->settings->get(SettingKey::CLINIC_IP);

        $this->assertEquals('bar', $result);
    }

    #[Test]
    public function itSetsAnArrayOfSettings(): void
    {
        $this->settings->set([
            SettingKey::AREAS->value => 'bang',
            SettingKey::ENCLOSURES->value => 'zap',
        ]);

        $result = $this->settings->get(SettingKey::AREAS);
        $this->assertEquals('bang', $result);

        $this->assertDatabaseHas('settings', [
            'team_id' => $this->team->id,
            'key' => SettingKey::AREAS->value,
            'value' => '"bang"',
        ]);

        $result = $this->settings->get(SettingKey::ENCLOSURES);
        $this->assertEquals('zap', $result);

        $this->assertDatabaseHas('settings', [
            'team_id' => $this->team->id,
            'key' => SettingKey::ENCLOSURES->value,
            'value' => '"zap"',
        ]);
    }

    #[Test]
    public function itSetsASetting(): void
    {
        $this->settings->set(SettingKey::SHOW_LOOKUP_RESCUER, 'value');

        $result = $this->settings->get(SettingKey::SHOW_LOOKUP_RESCUER);

        $this->assertEquals('value', $result);

        $this->assertDatabaseHas('settings', [
            'team_id' => $this->team->id,
            'key' => SettingKey::SHOW_LOOKUP_RESCUER->value,
            'value' => '"value"',
        ]);
    }

    #[Test]
    public function itUpdatesAnExistingSetting(): void
    {
        $this->settings->set([SettingKey::LOG_SHARES->value => 'fun']);

        $result = $this->settings->get(SettingKey::LOG_SHARES);
        $this->assertEquals('fun', $result);

        $this->assertDatabaseHas('settings', [
            'team_id' => $this->team->id,
            'key' => SettingKey::LOG_SHARES->value,
            'value' => '"fun"',
        ]);
    }
}
