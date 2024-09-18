<?php

namespace App\Repositories;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Incident;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class IncidentRepository
{
    /**
     * Get a paginated list of the open incidents.
     */
    public static function openIncidents(Team $team, $search = ''): LengthAwarePaginator
    {
        [$statusOpenId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::HOTLINE_STATUSES->value,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_OPEN->value
        ]);

        return static::baseQuery($team, $search)
            ->where('incident_status_id', $statusOpenId)
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of the resolved incidents.
     */
    public static function resolvedIncidents(Team $team, $search = ''): LengthAwarePaginator
    {
        [$statusResolvedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::HOTLINE_STATUSES->value,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value
        ]);

        return static::baseQuery($team, $search)
            ->where('incident_status_id', $statusResolvedId)
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of the unresolved incidents.
     */
    public static function unresolvedIncidents(Team $team, $search = ''): LengthAwarePaginator
    {
        [$statusUnresolvedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::HOTLINE_STATUSES->value,
            AttributeOptionUiBehavior::HOTLINE_STATUS_IS_UNRESOLVED->value
        ]);

        return static::baseQuery($team, $search)
            ->where('incident_status_id', $statusUnresolvedId)
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Get a paginated list of the deleted incidents.
     */
    public static function deletedIncidents(Team $team, $search = ''): LengthAwarePaginator
    {
        return static::baseQuery($team, $search)
            ->onlyTrashed()
            ->paginate()
            ->onEachSide(1);
    }

    /**
     * Base query from which all other queries build off of.
     */
    private static function baseQuery(Team $team, string $search = null): Builder
    {
        return Incident::query()
            ->with([
                'reportingParty',
                'status',
                'category'
            ])
            ->where('team_id', $team->id)
            ->when($search, fn ($query) => $query->likeOneOfMany($search, [
                'incident_number',
                'suspected_species',
                'incident_address',
                'incident_city',
                'description',
                'resolution',
            ])->orWhereHas('reportingParty', fn ($query) => $query->likeOneOfMany($search, [
                'first_name',
                'last_name',
            ])))
            ->orderBy('id', 'desc');
    }
}
