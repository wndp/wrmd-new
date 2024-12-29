<?php

namespace App\Caches;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Request;

class PatientSelector
{
    /**
     * Push a patientId into memory.
     */
    public static function add(string $patientId): void
    {
        Redis::connection('cache')->sadd(static::fingerprint(), $patientId);
        Redis::connection('cache')->expire(static::fingerprint(), 7200);
    }

    /**
     * Get patientIds in memory.
     */
    public static function get(): array
    {
        return array_map('intval', Redis::connection('cache')->smembers(static::fingerprint()));
    }

    /**
     * Count patientIds in memory.
     */
    public static function count(): int
    {
        return count(static::get());
    }

    /**
     * Determining if a patients are selected.
     */
    public static function exists(): bool
    {
        return (bool) Redis::connection('cache')->exists(static::fingerprint());
    }

    /**
     * Remove a patientId from memory.
     */
    public static function remove(string $patientId): void
    {
        Redis::connection('cache')->srem(static::fingerprint(), $patientId);
    }

    /**
     * Drop all patientIds from memory.
     */
    public static function empty(): void
    {
        Redis::connection('cache')->del(static::fingerprint());
    }

    /**
     * Flush all selected patients.
     */
    public static function flush(): void
    {
        foreach (Redis::connection('cache')->keys('selected_patient.*') as $key) {
            Redis::connection('cache')->del($key);
        }
    }

    /**
     * Get a unique fingerprint for the patient selector.
     */
    private static function fingerprint(): string
    {
        return 'selectedPatient.'.Request::cookie('device-uuid');
    }
}
