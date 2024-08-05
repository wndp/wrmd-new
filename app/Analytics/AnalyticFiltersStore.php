<?php

namespace App\Analytics;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;

class AnalyticFiltersStore
{
    private static function footprint()
    {
        return 'analytics.'.Request::cookie('device-uuid');
    }

    public static function add($key, $value)
    {
        Redis::hset(static::footprint(), $key, json_encode($value));
        Redis::expire(static::footprint(), 7200);
    }

    public static function get($key)
    {
        return static::decode(Redis::hget(static::footprint(), $key));
    }

    public static function update($key, $value)
    {
        return static::add($key, $value);
    }

    public static function all()
    {
        return static::decode(Redis::hgetall(static::footprint()));
    }

    public static function delete($key)
    {
        return Redis::hdel(static::footprint(), $key);
    }

    public static function destroy()
    {
        return Redis::del(static::footprint());
    }

    protected static function decode($hash)
    {
        $hash = collect($hash)->filter();

        return $hash->map(function ($value) {
            return json_decode($value);
        });
    }
}
