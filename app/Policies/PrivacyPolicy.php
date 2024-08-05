<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Wrmd;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrivacyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user is allowed to see people.
     */
    public function displayPeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('display-people')
            || $user->can('delete-people')
            || $user->can('combine-people')
            || $user->can('create-people')
            || $user->can('export-people');
    }

    /**
     * Determine if the user is allowed to see rescuers.
     */
    public function displayRescuer(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('display-rescuer');
    }

    /**
     * Determine if the user is allowed to search rescuers.
     */
    public function searchRescuers(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('search-rescuers');
    }

    /**
     * Determine if the user is allowed to combine people.
     */
    public function combinePeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('combine-people');
    }

    /**
     * Determine if the user is allowed to export people.
     */
    public function exportPeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('export-people');
    }

    /**
     * Determine if the user is allowed to export people.
     */
    public function createPeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('create-people');
    }

    /**
     * Determine if the user is allowed to delete people.
     */
    public function deletePeople(User $user): bool
    {
        return $this->hasFullPeopleAccess($user)
            || $user->isA('super-admin')
            || $user->can('delete-people');
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
