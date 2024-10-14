<?php

namespace App\Repositories;

use App\Enums\SettingKey;
use App\Models\Setting;
use App\Models\Team;
use ArrayIterator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use IteratorAggregate;
use JsonSerializable;

class SettingsStore implements IteratorAggregate, JsonSerializable
{
    private $team;

    protected $settings = [];

    /**
     * Create a new collection.
     *
     * @return void
     */
    public function __construct(Team $team)
    {
        if (! $team->exists) {
            return;
        }

        $this->team = $team;
        $this->settings = $team->settings->pluck('value', 'key')->all();
    }

    /**
     * Retrieve the given setting.
     *
     * @param  mixed  $default
     */
    public function get(SettingKey $key, $default = null)
    {
        $value = Arr::get($this->settings, $key->value, $default);

        return is_null($value) ? $default : $value;
    }

    /**
     * Create and persist a new setting.
     *
     * @param  SettingKey|array  $key
     * @param  mixed  $value
     */
    public function set(SettingKey|array $key, $value = null)
    {
        if ($key instanceof SettingKey) {
            $key = [$key->value => $value];
        }

        foreach ($key as $arrayKey => $arrayValue) {
            $this->settings[$arrayKey] = $arrayValue;
            $this->persist($arrayKey, $arrayValue);
        }

        $this->team->load('settings');
    }

    /**
     * Determine if the given setting exists.
     */
    public function has(SettingKey $key): bool
    {
        return array_key_exists($key->value, $this->settings);
    }

    /**
     * Retrieve an array of all settings.
     */
    public function all(): array
    {
        return $this->settings;
    }

    /**
     * Delete a setting from storage.
     */
    public function delete(SettingKey $key): bool
    {
        if ($this->has($key) && $this->isNotProtected($key)) {
            return (bool) Setting::where([
                'team_id' => $this->team->id,
                'key' => $key->value,
            ])->delete();
        }

        return false;
    }

    /**
     * Persist a setting.
     */
    protected function persist(string $key, $value): void
    {
        $setting = Setting::firstOrNew([
            'team_id' => $this->team->id,
            'key' => $key,
        ]);

        $setting->value = $value;
        $setting->team_id = $this->team->id;

        $setting->save();
    }

    /**
     * Determine if the provided key is not protected.
     */
    protected function isNotProtected(SettingKey $key): bool
    {
        return ! in_array($key->value, [
            SettingKey::TIMEZONE->value,
            SettingKey::LANGUAGE->value
        ]);
    }

    /**
     * Magic property access for settings.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
    }

    /**
     * Get an iterator for the items.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->settings);
    }

    /**
     * Specify data which should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return $this->all();
    }

    /**
     * How to react when settings is treated like a string.
     */
    public function __toString(): string
    {
        return json_encode($this->jsonSerialize());
    }
}
