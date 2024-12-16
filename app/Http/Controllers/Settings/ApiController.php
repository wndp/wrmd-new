<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Laravel\Jetstream\Events\TeamUpdated;
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

        $tokens = User::apiUserFor(Auth::user()->currentTeam)->tokens->sortBy('name');

        return Inertia::render('Settings/Api', compact('abilities', 'tokens'));
    }

    /**
     * Store a new API token.
     */
    public function store(Request $request)
    {
        $team = $request->user()->currentTeam;
        $apiUser = User::apiUserFor($team);

        $data = $request->validate([
            'token_name' => ['required', Rule::unique('\Laravel\Sanctum\PersonalAccessToken', 'name')->where(function ($query) use ($apiUser) {
                return $query->where('tokenable_id', $apiUser->id);
            })],
            'token_abilities' => ['required', 'array'],
        ]);

        $token = $apiUser->createToken($data['token_name'], array_values($data['token_abilities']));

        TeamUpdated::dispatch($team);

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __(':tokenName token was created.', [
                'tokenName' => $request->get('token_name'),
            ]))
            ->with('flash.token', $token);
    }

    /**
     * Delete an API token in storage.
     *
     * @param  \Laravel\Sanctum\PersonalAccessToken
     */
    public function delete(PersonalAccessToken $token)
    {
        $token->tokenable->validateOwnership(Auth::user()->currentTeam->id);

        $token->delete();

        TeamUpdated::dispatch(Auth::user()->currentTeam);

        return redirect()->route('api.index')
            ->with('notification.heading', __('Token Deleted!'))
            ->with('notification.text', __(':tokenName token was deleted.', [
                'tokenName' => $token->name,
            ]));
    }
}
