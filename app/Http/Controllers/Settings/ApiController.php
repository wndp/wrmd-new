<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Users\User;
use App\Events\AccountUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Laravel\Sanctum\PersonalAccessToken;

class ApiController extends Controller
{
    /**
     * Display the API settings.
     */
    public function index()
    {
        $abilities = [
            ['taxa:view', 'reports:view'],
            ['patients:view'],
            ['people:view'],
            ['donations:view'],
        ];

        $tokens = User::apiUserFor(Auth::user()->current_account)->tokens;

        return Inertia::render('Settings/Api', compact('abilities', 'tokens'));
    }

    /**
     * Store a new API token.
     */
    public function store(Request $request)
    {
        $account = $request->user()->current_account;
        $apiUser = User::apiUserFor($account);

        $data = $request->validate([
            'token_name' => ['required', Rule::unique('\Laravel\Sanctum\PersonalAccessToken', 'name')->where(function ($query) use ($apiUser) {
                return $query->where('tokenable_id', $apiUser->id);
            })],
            'token_abilities' => ['required', 'array'],
        ]);

        $token = $apiUser->createToken($data['token_name'], array_values($data['token_abilities']));

        event(new AccountUpdated($account));

        return redirect()->route('api.index')
            ->with('flash.notificationHeading', 'Success!')
            ->with('flash.notification', __(':tokenName token was created.', ['tokenName' => $request->get('token_name')]))
            ->with('flash.token', $token);
    }

    /**
     * Delete an API token in storage.
     *
     * @param  \Laravel\Sanctum\PersonalAccessToken
     */
    public function delete(PersonalAccessToken $token)
    {
        $token->tokenable->validateOwnership(auth()->user()->currentAccount->id);

        $token->delete();

        return redirect()->route('api.index')
            ->with('flash.notificationHeading', 'Token Deleted!')
            ->with('flash.notification', "$token->name token was deleted.");
    }
}
