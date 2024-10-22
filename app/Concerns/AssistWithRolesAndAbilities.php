<?php

namespace App\Concerns;

use App\Enums\Role as RoleEnum;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Models;
use Silber\Bouncer\Database\Role;

trait AssistWithRolesAndAbilities
{
    public function joinTeamWithRole(Team $team, RoleEnum $role)
    {
        $this->teams()->attach($team);

        BouncerFacade::scope()->to($team->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign($role->value)->to($this->id);

        return $this;
    }

    /**
     * Switch the user's role.
     */
    public function switchRoleTo(string $role): static
    {
        $this->getRoles()->each(
            fn ($role) => $this->retract($role)
        );

        $this->assign($role);

        return $this;
    }

    /**
     * Get the user's role on a given team.
     */
    public function roleOn(Team $team): Role|null
    {
        return $this->allRoles->firstWhere('pivot.scope', $team->id);

        // return $role ?: new Role([
        //     'name' => RoleEnum::USER->value,
        //     'title' => 'User'
        // ]);
    }

    /**
     * Get the user's role on the team currently being viewed.
     */
    public function roleOnCurrentTeam(): Role
    {
        return $this->roleOn($this->currentTeam);
    }

    /**
     * Get the name of the users role on their current team.
     */
    public function getAuthenticatedUsersCurrentRoleNameAttribute(): string
    {
        return $this->roleOnCurrentTeam()->name;
    }

    /**
     * Get the human friendly name of the users role on their current team.
     */
    public function getAuthenticatedUsersCurrentRoleNameForHumansAttribute(): string
    {
        return Str::of($this->getAuthenticatedUsersCurrentRoleNameAttribute())
            ->slug(' ')
            ->title()
            ->toString();
    }

    /**
     * Determine if the user has a viewer Role.
     */
    public function isAViewer(): bool
    {
        return $this->getAuthenticatedUsersCurrentRoleNameAttribute() === RoleEnum::VIEWER->value;
    }

    public function getRoleNameOnTeamForHumans($team)
    {
        return Str::of($this->roleOn($team)?->name)
            ->slug(' ')
            ->title()
            ->toString();
    }

    /**
     * The roles relationship.
     * Similar to \Silber\Bouncer\Database\Concerns\HasRoles@roles() but without scope limitation.
     */
    public function allRoles(): MorphToMany
    {
        return $this->morphToMany(
            Models::classname(Role::class),
            'entity',
            Models::table('assigned_roles')
        )->withPivot('scope');
    }

    public function scopeWhereCan(Builder $query, string $ability)
    {
        $query->where(function ($query) use ($ability) {
            // direct
            $query->whereHas('abilities', function ($query) use ($ability) {
                $query->byName($ability);
            });

            // through roles
            $query->orWhereHas('roles', function ($query) use ($ability) {
                $query->whereHas('abilities', function ($query) use ($ability) {
                    $query->byName($ability);
                });
            });
        });
    }
}
