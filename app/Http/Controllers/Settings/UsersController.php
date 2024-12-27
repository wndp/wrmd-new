<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Ability as AbilityEnum;
use App\Enums\Role as RoleEnum;
use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveNewUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Options\Options;
use App\Repositories\OptionsStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\Mail\TeamInvitation;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\Ability;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = Auth::user()
            ->currentTeam
            ->allUsers()
            ->load('roles')
            ->filter(fn ($user) => ! Str::startsWith($user->email, 'api-user'))
            ->values()
            ->transform(function ($user) {
                $user->role_name_for_humans = $user->getRoleNameOnTeamForHumans(Auth::user()->currentTeam);

                return $user;
            });

        return Inertia::render('Settings/Users/Index', compact('users'));
    }

    /**
     * Search existing users by email address.
     */
    public function search(Request $request): JsonResponse
    {
        $result = User::whereEmail($request->email)->first();

        return response()->json($result);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): Response
    {
        OptionsStore::add([
            'roles' => Options::enumsToSelectable(RoleEnum::publicRoles()),
        ]);

        return Inertia::render('Settings/Users/Create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(SaveNewUserRequest $request): RedirectResponse
    {
        $invitation = Auth::user()->currentTeam->teamInvitations()->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
        ]);

        Mail::to($request->input('email'))->send(new TeamInvitation($invitation));

        return redirect()->route('users.index')
            ->with('notification.heading', __('User Invited'))
            ->with('notification.text', __(':userName was invited to your account.', ['userName' => $request->input('name')]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        OptionsStore::add([
            'roles' => Options::enumsToSelectable(RoleEnum::publicRoles()),
        ]);

        abort_unless($user->belongsToTeam(Auth::user()->currentTeam), new RecordNotOwned);

        $user->role_name = $user->roleOn(Auth::user()->currentTeam)?->name;
        $abilities = Ability::whereIn('name', AbilityEnum::publicAbilities())->get();
        $allowedAbilities = $user->getAbilities();
        $forbiddenAbilities = $user->getForbiddenAbilities();
        $unAllowedAbilities = $abilities->diff($allowedAbilities);

        return Inertia::render('Settings/Users/Edit', compact(
            'user',
            'abilities',
            'allowedAbilities',
            'forbiddenAbilities',
            'unAllowedAbilities'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->belongsToTeam(Auth::user()->currentTeam), new RecordNotOwned);

        $user->update($request->only('name', 'email'));

        $user->switchRoleTo($request->role);
        BouncerFacade::disallow($user)->to(Ability::all());
        BouncerFacade::refreshFor($user);

        return redirect()->back()
            ->with('notification.heading', __('User Updated'))
            ->with('notification.text', __(':userName profile was updated.', ['userName' => $user->name]));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->belongsToTeam(Auth::user()->currentTeam), new RecordNotOwned);

        Auth::user()->currentTeam->removeUser($user);

        //$user->leaveAccount(Auth::user()->currentTeam);

        return redirect()->route('users.index')
            ->with('notification.heading', __('User Deleted'))
            ->with('notification.text', __(':userName was deleted from your account.', ['userName' => $user->name]));
    }
}
