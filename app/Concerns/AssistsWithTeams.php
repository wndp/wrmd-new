<?php

namespace App\Concerns;

use App\Enums\Role;
use App\Models\Team;
use Silber\Bouncer\BouncerFacade;

trait AssistsWithTeams
{
    public function teammatesOnTeam(Team $team)
    {
        return $team
            ->allUsers()
            ->where('user_id', '!=', $this->id);
    }

    public function joinTeam(Team $team, Role $role)
    {
        if ($this->fresh()->belongsToTeam($team)) {
            return $this;
        }

        $team->users()->attach($this);

        BouncerFacade::scope()->to($team->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign($role->value)->to($this->id);

        return $this;
    }

    public function belongsToAnyTeams()
    {
        return $this->teams->count() > 0;
    }
}
