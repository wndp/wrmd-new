<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;
use Silber\Bouncer\Database\Role;

class AdminAuthorizationController extends Controller
{
    /**
     * Display a listing of the authorization models.
     */
    public function edit(): Response
    {
        $roles = Role::with(['abilities' => function ($query) {
            $query->addSelect('abilities.*', 'forbidden');
        }])
            ->orderBy('id')
            ->get();

        $abilities = Ability::get();

        return Inertia::render('Admin/Authorization', compact('roles', 'abilities'));
    }

    /**
     * Update the roles default abilities.
     */
    public function update(Request $request, string $association): RedirectResponse
    {
        $roles = Role::get();

        $data = $request->validate(
            $roles->mapWithKeys(function ($role) {
                return [$role->name => 'nullable|array'];
            })->toArray(),
            $roles->mapWithKeys(function ($role) use ($association) {
                return ["$role->name.array" => "The $role->name $association abilities must be an array."];
            })->toArray()
        );

        switch ($association) {
            case 'allowed':
                $this->giveAbilities($roles, $data);
                break;

            case 'forbidden':
                $this->forbidAbilities($roles, $data);
                break;

            default:
                throw new Exception("Unrecognizable authorization association [{$association}]", 1);
                break;
        }

        BouncerFacade::refresh();

        return redirect()->route('admin.authorization')
            ->with('flash.notificationHeading', 'Success')
            ->with('flash.notification', "Default $association role abilities saved.");
    }

    /**
     * Give the requested abilities to existing roles.
     *
     * @return void
     */
    public function giveAbilities($roles, $data)
    {
        foreach ($roles as $role) {
            $this->removeAbilitiesFromRole($role);

            if ($abilities = $data[$role->name]) {
                BouncerFacade::allow($role->name)->to($abilities);
            }
        }
    }

    /**
     * Forbid the requested abilities to existing roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function forbidAbilities($roles, $data)
    {
        foreach ($roles as $role) {
            $this->removeAbilitiesFromRole($role, true);

            if ($abilities = $data[$role->name]) {
                BouncerFacade::forbid($role->name)->to($abilities);
            }
        }
    }

    /**
     * Remove all current unforbidden abilities from the provided role.
     *
     * @return void
     */
    public function removeAbilitiesFromRole(Role $role, bool $forbidden = false)
    {
        $role->abilities()->wherePivot('forbidden', (int) $forbidden)->detach();
    }
}
