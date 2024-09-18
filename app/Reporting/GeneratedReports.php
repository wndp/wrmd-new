<?php

namespace App\Reporting;

use App\Models\Team;
use App\Reporting\Reports\AssetReport;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class GeneratedReports
{
    /**
     * The number of minutes to cache for.
     *
     * @var int
     */
    protected static $minutes = 5;

    /**
     * Get an accounts generated reports saved in storage.
     */
    public static function get(Team $team): Collection
    {
        return Cache::remember(
            'GeneratedReports.'.$team->id,
            now()->addMinutes(static::$minutes),
            function () use ($team) {
                try {
                    return static::allGeneratedReports($team)->map(function ($file) {
                        $createdAt = static::createdAt($file);

                        return tap(new AssetReport(
                            Storage::temporaryUrl($file, now()->addMinutes(static::$minutes * 5)),
                            static::createdAtForHumans($createdAt).' :: '.basename($file),
                            $createdAt
                        ), function ($report) use ($createdAt) {
                            $report->createdAt = $createdAt;
                        });
                    })
                        ->sortByDesc(function ($report) {
                            return $report->createdAt->timestamp;
                        })
                        ->values();
                } catch (Exception $e) {
                    return [];
                }
            }
        );
    }

    /**
     * Get an accounts generated reports saved in storage.
     */
    protected static function allGeneratedReports(Team $team): Collection
    {
        return collect(Storage::allFiles("generated-reports/{$team->id}"));
    }

    /**
     * Get a files created at date as a Carbon instance.
     */
    protected static function createdAt(string $file): Carbon
    {
        $microTime = base64_decode(explode('/', $file)[2]);
        $microTimeParts = explode(' ', $microTime);

        return Carbon::createFromTimestamp($microTimeParts[1], settings('timezone'));
    }

    /**
     * Format a files created_at timestamp into human readable version.
     */
    protected static function createdAtForHumans(Carbon $date): string
    {
        return $date->toDayDateTimeString();
    }
}
