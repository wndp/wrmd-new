<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Enums\Role;
use App\Models\User;
use App\Support\Wrmd;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivacyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user is allowed to see people.
     */
    public function viewPeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::VIEW_PEOPLE->value)
            || $user->can(Ability::DELETE_PEOPLE->value)
            || $user->can(Ability::COMBINE_PEOPLE->value)
            || $user->can(Ability::CREATE_PEOPLE->value)
            || $user->can(Ability::EXPORT_PEOPLE->value);
    }

    /**
     * Determine if the user is allowed to see rescuers.
     */
    public function viewRescuer(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::VIEW_RESCUER->value);
    }

    /**
     * Determine if the user is allowed to search rescuers.
     */
    public function searchRescuers(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::SEARCH_RESCUERS->value);
    }

    /**
     * Determine if the user is allowed to combine people.
     */
    public function combinePeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::COMBINE_PEOPLE->value);
    }

    /**
     * Determine if the user is allowed to export people.
     */
    public function exportPeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::EXPORT_PEOPLE->value);
    }

    /**
     * Determine if the user is allowed to export people.
     */
    public function createPeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::CREATE_PEOPLE->value);
    }

    /**
     * Determine if the user is allowed to delete people.
     */
    public function deletePeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA(Role::ADMIN->value)
            || $user->can(Ability::DELETE_PEOPLE->value);
    }

    /**
     * Determine if the users current account has granted full access to the people.
     */
    private function hasFullPeopleAccess($user): bool
    {
        if (is_null($user->current_team_id)) {
            return false;
        }

        return (int) Wrmd::settings('fullPeopleAccess') === 1;
    }
}
