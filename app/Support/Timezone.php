<?php

namespace App\Support;

use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Timezone
{
    public static function convertFromUtcToLocal(?Carbon $date, Team $team = null): ?Carbon
    {
        if (Auth::guest() || is_null($date)) {
            return null;
        }

        return $date->setTimezone(static::getTimezone($team));
    }

    public static function convertFromLocalToUtc($date, Team $team = null): ?Carbon
    {
        if (Auth::guest() || is_null($date)) {
            return null;
        }

        return Carbon::parse($date, static::getTimezone($team))->setTimezone('UTC');
    }

    /**
     * Get the user's team timezone.
     */
    private static function getTimezone(Team $team = null): string
    {
        $team = $team ?: Auth::user()->currentTeam;

        return Cache::remember(
            'timezone.'.$team->id,
            Carbon::tomorrow(),
            fn () => $team->settingsStore()->get('timezone', 'UTC'),
        );
    }
}
