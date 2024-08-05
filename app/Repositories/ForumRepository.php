<?php

namespace App\Repositories;

use App\Enums\Channel;
use App\Models\ForumGroup;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ForumRepository
{
    public static function forgetGroupsFor(Team $team)
    {
        Cache::forget("forum.groups.team.{$team->id}");
    }

    public static function groupsFor(Team $team)
    {
        return Cache::remember(
            "forum.groups.team.{$team->id}",
            now()->addHours(3),
            fn () => ForumGroup::whereHas(
                'members',
                fn ($query) => $query->where('team_id', $team->id)
            )->get()
        );
    }

    public static function channels()
    {
        return Cache::remember(
            'forum.channels.public',
            Carbon::now()->addHours(3),
            fn () => Collection::make(Channel::cases())
                ->reject(Channel::WELCOME)
                ->map(fn ($channel) => [
                    'value' => $channel->value,
                    'label' => $channel->label(),
                ])->values()->toArray()
        );
    }
}
