<?php

namespace App\Caches;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;

class QueryCache
{
    /**
     * Get a unique fingerprint for the query cache.
     */
    private static function fingerprint(): string
    {
        return 'queryCache.'.Request::cookie('device-uuid');
    }

    /**
     * Components to bindings map.
     *
     * @var array
     */
    private static $componentsBindingsMap = [
        'select' => 'columns',
        'join' => 'joins',
        'where' => 'wheres',
        'having' => 'havings',
        'order' => 'orders',
    ];

    /**
     * Remove an item in the queryCache.
     */
    public function remove(string $key): void
    {
        // $queryCache = $this->get();

        // if (! array_key_exists($key, $queryCache)) {
        //     return;
        // }

        // unset($queryCache[$key]);

        // $this->session->set(self::SESSION_KEY, $queryCache);
    }

    /**
     * Push a query into the queryCache.
     */
    public static function add(array $components, array $bindings = [])
    {
        Redis::connection('cache')->set(static::fingerprint(), json_encode(compact('components', 'bindings')));
        Redis::connection('cache')->expire(static::fingerprint(), 7200);
    }

    /**
     * Determining if a queryCache exists.
     */
    public static function exists(): bool
    {
        return ((bool) Redis::connection('cache')->exists(static::fingerprint())) && SearchCache::exists();
    }

    /**
     * Retrieve an item from the queryCache session.
     *
     * @return mixed
     */
    public static function get(int $key = null)
    {
        return $queryCache = json_decode(Redis::connection('cache')->get(static::fingerprint()));

        //return array_key_exists($key, $queryCache) ? $queryCache[$key] : $queryCache;
    }

    /**
     * Removing all items from the queryCache.
     */
    public static function empty(): void
    {
        Redis::connection('cache')->del(static::fingerprint());
        SearchCache::empty();
    }

    /**
     * Hydrate a component in the queryCache.
     */
    public static function hydrate(string $component): string
    {
        $queryCache = static::get();

        $sql = $queryCache->components->{static::$componentsBindingsMap[$component]};
        $bindings = $queryCache->bindings->$component;

        $sql = static::sanitize($sql, $component);

        return vsprintf($sql, $bindings);
    }

    /**
     * Return the results of the queryCache searchCache results.
     */
    public static function results(): \StdClass
    {
        return SearchCache::get();
    }

    /**
     * Sanitize a sql component for xxx.
     */
    private static function sanitize(string $sql, string $component): string
    {
        $sql = substr($sql, strlen($component));
        $sql = str_replace('"', '`', $sql);
        $sql = str_replace('?', '"%s"', $sql);

        return $sql;
    }
}
