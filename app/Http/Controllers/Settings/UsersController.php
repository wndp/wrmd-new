<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Database\RecordNotOwnedResponse;
use App\Domain\OptionsStore;
use App\Domain\Users\User;
use App\Domain\Users\UserOptions;
use App\Events\NewUser;
use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Rules\SuperAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
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
            ->currentAccount
            ->users()
            ->with('roles')
            ->get()
            ->filter(fn ($user) => ! Str::startsWith($user->email, 'api-user'))
            ->values()
            ->transform(function ($user) {
                $user->role_name_for_humans = $user->getRoleNameOnAccountForHumans(Auth::user()->currentAccount);

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
    public function create(UserOptions $options): Response
    {
        OptionsStore::merge($options);

        return Inertia::render('Settings/Users/Create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'role' => ['required', Rule::in(UserOptions::$roles)],
        ]);

        $currentAccount = Auth::user()->currentAccount;
        $user = User::whereEmail($request->email)->first();

        if (! $user) {
            $request->validate([
                'name' => 'required',
                'email' => 'confirmed',
                'password' => 'required|confirmed',
            ]);

            $user = User::create([
                'parent_account_id' => $currentAccount->id,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'name' => $request->name,
            ]);
        }

        $user->joinAccount($currentAccount, $request->role);

        if ($request->send_email) {
            Mail::send(new WelcomeEmail($user, $currentAccount, $request->password));
        }

        event(new NewUser($user, $currentAccount));

        return redirect()->route('users.index')
            ->with('flash.notificationHeading', __('User Created'))
            ->with('flash.notification', __(':userName was added to your account.', ['userName' => $user->name]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, UserOptions $options): Response
    {
        OptionsStore::merge($options);

        abort_unless($user->inAccount(Auth::user()->currentAccount), new RecordNotOwnedResponse());

        $user->role_name_for_humans = $user->getRoleNameOnAccountForHumans(Auth::user()->currentAccount);
        $abilities = Ability::get();
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
        abort_unless($user->inAccount(Auth::user()->currentAccount), new RecordNotOwnedResponse());

        if ($user->parent_account_id === Auth::user()->current_account_id) {
            $request->validate([
                'email' => 'required|email|confirmed|unique:users,email,'.$user->id,
                'password' => 'nullable|confirmed',
                'name' => 'required',
                'role' => ['required', Rule::in(UserOptions::$roles), new SuperAdmin(Auth::user())],
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }

            $user->update($request->all('name', 'email'));
        } else {
            $request->validate([
                'role' => ['required', new SuperAdmin($user), Rule::in(UserOptions::$roles)],
            ]);
        }

        $user->switchRoleTo($request->role);

        BouncerFacade::disallow($user)->to(Ability::all());
        BouncerFacade::refreshFor($user);

        return redirect()->back()
            ->with('flash.notificationHeading', __('User Updated'))
            ->with('flash.notification', __(':userName profile was updated.', ['userName' => $user->name]));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->inAccount(Auth::user()->currentAccount), new RecordNotOwnedResponse());

        $user->leaveAccount(Auth::user()->currentAccount);

        return redirect()->route('users.index')
            ->with('flash.notificationHeading', __('User Deleted'))
            ->with('flash.notification', __(':userName was deleted from your account.', ['userName' => $user->name]));
    }
}
