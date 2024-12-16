<?php

namespace App\Http\Controllers\Settings;

use App\Enums\Ability as AbilityEnum;
use App\Enums\Role as RoleEnum;
use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use App\Options\Options;
use App\Repositories\OptionsStore;
use App\Rules\Admin;
use App\Rules\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Jetstream\Contracts\AddsTeamMembers;
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
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'role' => new Role,
        ]);

        $user = User::whereEmail($request->email)->first();

        if (! $user) {
            $request->validate([
                'email' => 'confirmed|unique:users',
                'password' => 'required|confirmed',
                'name' => 'required',
            ]);

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'name' => $request->name,
            ]);
        }

        app(AddsTeamMembers::class)->add(
            Auth::user(),
            Auth::user()->currentTeam,
            $request->email ?: '',
            $request->role
        );

        if ($request->send_email) {
            Mail::send(new WelcomeEmail($user, Auth::user()->currentTeam, $request->password));
        }

        return redirect()->route('users.index')
            ->with('notification.heading', __('User Created'))
            ->with('notification.text', __(':userName was added to your account.', ['userName' => $user->name]));
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
    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->belongsToTeam(Auth::user()->currentTeam), new RecordNotOwned);

        if ($user->parent_account_id === Auth::user()->current_team_id) {
            $request->validate([
                'email' => 'required|email|confirmed|unique:users,email,'.$user->id,
                'password' => 'nullable|confirmed',
                'name' => 'required',
                'role' => ['required', Rule::enum(RoleEnum::class), new Admin(Auth::user())],
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }

            $user->update($request->all('name', 'email'));
        } else {
            $request->validate([
                'role' => ['required', new Admin($user), Rule::enum(RoleEnum::class)],
            ]);
        }

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
        abort_unless($user->inAccount(Auth::user()->currentTeam), new RecordNotOwned);

        $user->leaveAccount(Auth::user()->currentTeam);

        return redirect()->route('users.index')
            ->with('notification.heading', __('User Deleted'))
            ->with('notification.text', __(':userName was deleted from your account.', ['userName' => $user->name]));
    }
}
