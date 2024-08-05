<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class OperationsPolicy
{
    use HandlesAuthorization;

    public function displaySettings($user)
    {
        return true;

        if ($user->currentTeam?->isSubAccount()) {
            if ($user->roleOn($user->currentTeam->masterAccount)->name === 'super-admin') {
                return true;
            } elseif ($user->currentTeam->settingsStore()->get('subAccountAllowManageSettings') === false) {
                return false;
            }
        }

        return $user->isA('super-admin', 'admin');
    }

    public function displayMaintenance($user)
    {
        return $user->isA('super-admin', 'admin');
    }

    public function displayTransferPatient($user)
    {
        if ($user->currentTeam?->settingsStore()->get('subAccountAllowTransferPatients') === false) {
            return false;
        }

        return $user->can('share-patients');
    }

    public function viewSubAccounts($user)
    {
        if (is_null($user->current_account_id)) {
            return false;
        }

        return $user->currentTeam?->is_master_account && $user->isA('super-admin');
    }
}
