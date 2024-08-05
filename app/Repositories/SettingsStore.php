<?php

namespace App\Repositories;

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
    public function get(string $key, $default = null)
    {
        $value = Arr::get($this->settings, $key, $default);

        return is_null($value) ? $default : $value;
    }

    /**
     * Create and persist a new setting.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     */
    public function set($key, $value = null)
    {
        if (! is_array($key)) {
            $key = [$key => $value];
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
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->settings);
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
    public function delete(string $key): bool
    {
        if ($this->has($key) && $this->isNotProtected($key)) {
            return (bool) Setting::where([
                'team_id' => $this->team->id,
                'key' => $key,
            ])->delete();
        }

        return false;
    }

    /**
     * Persist a setting.
     */
    protected function persist($key, $value): void
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
    protected function isNotProtected(string $key): bool
    {
        return ! in_array($key, [
            'date_format',
            'timezone',
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
