<?php

namespace App\Repositories;

use App\Models\Person;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class PeopleRepository
{
    /**
     * Get a paginated list of a teams rescuers.
     */
    public static function rescuers(Team $team, $search = ''): LengthAwarePaginator
    {
        return static::baseQuery($team, $search)
            ->has('patients')
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of a teams hotline reporting parties.
     */
    public static function reportingParties(Team $team, $search = ''): LengthAwarePaginator
    {
        return static::baseQuery($team, $search)
            ->has('hotline')
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of a teams volunteers.
     */
    public static function volunteers(Team $team, $search = ''): LengthAwarePaginator
    {
        return static::baseQuery($team, $search)
            ->where('is_volunteer', true)
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of a teams members.
     */
    public static function members(Team $team, $search = ''): LengthAwarePaginator
    {
        return static::baseQuery($team, $search)
            ->where('is_member', true)
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of a teams donors.
     */
    public static function donors(Team $team, $search = ''): LengthAwarePaginator
    {
        return static::baseQuery($team, $search)
            ->has('donations')
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Base query from which all other queries build off of.
     */
    private static function baseQuery(Team $team, string $search = null): Builder
    {
        return Person::where('team_id', $team->id)
            ->whereRaw("concat(coalesce(`organization`, ''), coalesce(`first_name`, ''), coalesce(`last_name`, '')) != ''")
            ->when($search, fn ($query) => $query->likeOneOfMany($search, [
                'organization',
                'first_name',
                'last_name',
                'email',
                'phone',
                'alt_phone',
            ]))
            ->orderBy('last_name')
            ->orderBy('first_name');
    }
}
