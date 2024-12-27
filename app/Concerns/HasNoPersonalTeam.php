<?php

namespace App\Concerns;

trait HasNoPersonalTeam
{
    /**
     * Determine if the user owns the given team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function ownsTeam($team)
    {
        return $this->id == optional($team)->user_id;
    }

    /**
     * Determine if the given team is the current team.
     *
     * @param  mixed  $team
     * @return bool
     */
    public function isCurrentTeam($team)
    {
        return optional($team)->id === $this->currentTeam->id;
    }

    /**
     * Determine if the user is apart of any team.
     *
     * @param  mixed  $team
     */
    public function isMemberOfATeam(): bool
    {
        return (bool) ($this->teams()->count() || $this->ownedTeams()->count());
    }
}
