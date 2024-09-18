<?php

namespace App\Reporting\Filters;

use App\Models\Team;
use App\Reporting\Contracts\Filter;
use App\Reporting\Contracts\Report;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class TeamFilter extends Filter
{
    protected $report;

    protected $team;

    public function __construct(Report $report, Team $team)
    {
        $this->report = $report;
        $this->team = $team;
    }

    public function name(): string
    {
        return __('Team');
    }

    /**
     * The name of the Vue component to be used for this filter
     */
    public function component(): string
    {
        return 'GenericSelect';
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  mixed  $value
     */
    public function apply(Fluent $request, Builder $query, $value): Builder
    {
        $this->team
            ->subAccounts
            ->pluck('id', 'name')
            ->prepend($this->team->id, $this->team->name);
        //->toArray();

        return $query;
    }

    /**
     * Get the filters default value.
     *
     * @return string|array
     */
    public function default()
    {
        return $this->team->id;
    }

    public function options(): array
    {
        return $this->team
            ->subAccounts
            ->pluck('organization', 'id')
            ->prepend($this->team->organization, $this->team->id)
            ->toArray();
    }

    public function setTeamOnReport()
    {
        $teamId = $this->report->getAppliedFilterValue($this, $this->team->id);

        $this->report->setTeam(Team::find($teamId));
    }
}
