<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Veterinarian;
use App\Options\LocaleOptions;
use App\Options\Options;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VeterinariansController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $veterinarians = Veterinarian::where('team_id', Auth::user()->currentTeam->id)
            ->get()
            ->append('full_address');

        return Inertia::render('Settings/Veterinarians/Index', compact('veterinarians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(LocaleOptions $options)
    {
        OptionsStore::add($options);

        $users = Options::arrayToSelectable(
            Auth::user()->currentTeam->allUsers()->where('is_api_user', false)->pluck('name', 'id')->toArray()
        );

        return Inertia::render('Settings/Veterinarians/Create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'license' => 'required',
        ]);

        $veterinarian = tap(new Veterinarian($request->only(
            'name',
            'license',
            'business_name',
            'address',
            'city',
            'subdivision',
            'postal_code',
            'phone',
            'email',
        )), function ($veterinarian) use ($request) {
            $veterinarian->team_id = Auth::user()->currentTeam->id;
            $veterinarian->user_id = $request->input('user_id');
            $veterinarian->save();
        });

        return redirect()->route('veterinarians.index')
            ->with('notification.heading', __('Veterinarian Created'))
            ->with('notification.text', __(':name was added to your account.', ['name' => $veterinarian->name]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Veterinarian $veterinarian, LocaleOptions $options)
    {
        OptionsStore::add($options);

        $veterinarian->validateOwnership(Auth::user()->currentTeam->id);

        $users = Options::arrayToSelectable(
            Auth::user()->currentTeam->allUsers()->where('is_api_user', false)->pluck('name', 'id')->toArray()
        );

        return Inertia::render('Settings/Veterinarians/Edit', compact('users', 'veterinarian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Veterinarian $veterinarian)
    {
        $request->validate([
            'name' => 'required',
            'license' => 'required',
        ]);

        $veterinarian->validateOwnership(Auth::user()->currentTeam->id);

        $veterinarian = tap($veterinarian->fill($request->only(
            'name',
            'license',
            'business_name',
            'address',
            'city',
            'subdivision',
            'postal_code',
            'phone',
            'email',
        )), function ($veterinarian) use ($request) {
            $veterinarian->user_id = $request->input('user_id');
            $veterinarian->save();
        });

        return redirect()->route('veterinarians.index')
            ->with('notification.heading', __('Veterinarian Updated'))
            ->with('notification.text', __(':name was updated.', ['name' => $veterinarian->name]));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Veterinarian $veterinarian)
    {
        $veterinarian->validateOwnership(Auth::user()->currentTeam->id);

        $veterinarian->delete();

        return redirect()->route('veterinarians.index')
            ->with('notification.heading', __('Veterinarian Deleted'))
            ->with('notification.text', __(':name was deleted from your account.', ['name' => $veterinarian->name]));
    }
}
