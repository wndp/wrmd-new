<?php

namespace App\Repositories;

use App\Enums\AccountStatus;
use App\Http\Resources\AccountResource;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class TeamRepository
{
    public static function active()
    {
        return Cache::remember(
            'accounts.active',
            now()->addHours(6),
            fn () => AccountResource::collection(Team::where('status', AccountStatus::ACTIVE)->get())->values()
        );
    }
}
