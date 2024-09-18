<?php

namespace App\Http\Controllers\People;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PersonDonationsController extends Controller
{
    /**
     * Return a listing of a persons donations.
     */
    public function index(Person $person, PeopleOptions $options): Response
    {
        OptionsStore::merge($options);

        $person->validateOwnership(Auth::user()->current_team_id);

        $person->load('donations');

        return Inertia::render('People/Donations', compact('person'));
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(Request $request, Person $person): RedirectResponse
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'donated_at' => 'required|date',
            'method' => 'required',
            'value' => 'numeric',
        ], [
            'donated_at.required' => 'The donation date field is required.',
            'donated_at.date' => 'The donation date is not a valid date.',
        ]);

        $donation = new Donation($request->only([
            'donated_at',
            'method',
            'value',
            'comments',
        ]));

        $donation->person_id = $person->id;
        $donation->save();

        return redirect()->route('people.donations.index', $person);
    }

    /**
     * Update the specified donation in storage.
     */
    public function update(Request $request, Person $person, Donation $donation): RedirectResponse
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'donated_at' => 'required|date',
            'method' => 'required',
            'value' => 'numeric',
        ], [
            'donated_at.required' => 'The donation date field is required.',
            'donated_at.date' => 'The donation date is not a valid date.',
        ]);

        $donation->update($request->only([
            'donated_at',
            'method',
            'value',
            'comments',
        ]));

        return redirect()->route('people.donations.index', $person);
    }

    /**
     * Remove the specified donation from storage.
     */
    public function destroy(Person $person, Donation $donation): RedirectResponse
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $donation->delete();

        return redirect()->route('people.donations.index', $person);
    }
}
