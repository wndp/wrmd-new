<?php

namespace App\Support;

use App\Models\Admission;
use App\Repositories\SettingsStore;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Wrmd
{
    /**
     * The registered resource names.
     *
     * @var array
     */
    //public static $resources = [];

    /**
     * An index of resource names keyed by the model name.
     *
     * @var array
     */
    //public static $resourcesByModel = [];

    /**
     * Register the given resources.
     */
    // public static function resources(string|array $resources): static
    // {
    //     static::$resources = array_merge(static::$resources, Arr::wrap($resources));

    //     return new static();
    // }

    /**
     * Get the resource class name for a given model class.
     *
     * @param  object|string  $class
     */
    // public static function resourceForModel($class): ?string
    // {
    //     if (is_object($class)) {
    //         $class = get_class($class);
    //     }

    //     if (isset(static::$resourcesByModel[$class])) {
    //         return static::$resourcesByModel[$class];
    //     }

    //     $resource = collect(static::$resources)->first(function ($value) use ($class) {
    //         return $value::$resourceKey === $class;
    //     });

    //     return static::$resourcesByModel[$class] = $resource;
    // }

    /**
     * Find a model by its resource key and primary key or throw an exception.
     *
     * @param  mixed  $class
     */
    // public static function resourceForModelOrFail($class, int $id): Model
    // {
    //     $model = static::resourceForModel($class);

    //     abort_if(is_null($model), 404);

    //     try {
    //         return $model::findOrFail($id);
    //     } catch (ModelNotFoundException $e) {
    //         abort(404);
    //     }
    // }

    /**
     * Humanize the given value into a proper name.
     */
    public static function humanize(string|object $value): string
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        } else {
            return Str::title(Str::snake($value, ' '));
        }
    }

    /**
     * Get the URI key for a resource.
     */
    public static function uriKey($value): string
    {
        if (is_object($value)) {
            return static::uriKey(class_basename(get_class($value)));
        } else {
            return Str::kebab($value);
        }
    }

    /**
     * Get the badge color for the given model.
     */
    // public static function badge(string $value): string
    // {
    //     return 'green';
    // }

    public static function settings($key = null, $default = null)
    {
        $settings = app(SettingsStore::class);

        if (is_null($settings)) {
            return $default;
        }

        if (is_null($key)) {
            return $settings;
        }

        if (is_array($key)) {
            return $settings->set($key);
        }

        return $settings->get($key, $default);
    }

    public static function patientRoute(Admission $admission)
    {
        // dd($admission->patient->admitted_at);
        // $now = is_float($admission->patient->admitted_at) ? microtime(true) : time();
        // $time = $now - $admission->patient->admitted_at;

        $isWithin24hr = $admission->patient->admitted_at->greaterThan(Carbon::now()->subDays(1)); //$time <= 86400;
        $defaultRoute = $isWithin24hr ? 'patients.initial.edit' : 'patients.continued.edit';

        $events = event('patient_route', $admission->patient);
        $route = empty($events) || empty($events[0]) ? $defaultRoute : $events[0];

        //if (! empty($parameters)) {
        return route($route, ['y' => $admission->case_year, 'c' => $admission->case_id]);
        //}

        //return $route;
    }

    // public static function iso3166(string $country, string $subdivision = null): string
    // {
    //     return strtoupper(
    //         implode('-', array_filter(
    //             [$country, $subdivision]
    //         ))
    //     );
    // }
}
