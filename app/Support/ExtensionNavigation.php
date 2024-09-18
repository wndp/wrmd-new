<?php

namespace App\Support;

use Illuminate\Support\Arr;

class ExtensionNavigation
{
    /**
     * The merged options.
     *
     * @var array
     */
    public static $events = [];

    public static function clearCache()
    {
        static::$events = [];
    }

    public static function all()
    {
        return static::$events;
    }

    public static function emit($group, $args = [])
    {
        $args = Arr::wrap($args);

        foreach (static::$map[$group] as $event) {
            $listener = array_filter(event($event, ...$args));
            if (! empty($listener)) {
                static::$events = array_merge(static::$events, [$event => $listener]);
            }
        }
    }

    private static $map = [
        'patient' => [
            'patient.tabs_component',
            'patient.tabs',
            'patient.tabs.more',
            'patient.tabs.more.group',
            'patient.tabs.share',
            'patient.tabs.share.group',
            'patient.create.header',
            'patient.customFields',
        ],
        'searching' => [
            'searching.tabs',
        ],
        'maintenance' => [
            'maintenance.tabs',
        ],
    ];
}
