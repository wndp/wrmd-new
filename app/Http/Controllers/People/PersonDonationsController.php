<?php

namespace App\Http\Controllers\People;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveDonationRequest;
use App\Models\AttributeOption;
use App\Models\Donation;
use App\Models\Person;
use App\Repositories\OptionsStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PersonDonationsController extends Controller
{
    /**
     * Return a listing of a persons donations.
     */
    public function index(Person $person): Response
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::DONATION_METHODS->value,
            ]),
        ]);

        [$donationMethodIsCashId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::DONATION_METHODS->value,
            AttributeOptionUiBehavior::DONATION_METHOD_IS_CASH->value,
        ]);

        return Inertia::render('People/Donations', [
            'person' => $person,
            'donations' => $person->donations->transform(fn ($donation) => [
                'id' => $donation->id,
                'value' => $donation->value,
                'value_for_input' => $donation->value / 100,
                'value_for_humans' => $donation->value_for_humans,
                'donated_at' => $donation->donated_at,
                'donated_at_for_humans' => $donation->donated_at?->translatedFormat(config('wrmd.date_format')),
                'method_id' => $donation->method_id,
                'method' => $donation->method?->value,
                'comments' => $donation->comments,
            ]),
            'donationMethodIsCashId' => $donationMethodIsCashId,
        ]);
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(SaveDonationRequest $request, Person $person): RedirectResponse
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        Donation::create([
            'person_id' => $person->id,
            'donated_at' => $request->input('donated_at'),
            'method_id' => $request->integer('method_id'),
            'value' => $request->input('value') * 100,
            'comments' => $request->input('comments'),
        ]);

        return redirect()->route('people.donations.index', $person);
    }

    /**
     * Update the specified donation in storage.
     */
    public function update(SaveDonationRequest $request, Person $person, Donation $donation): RedirectResponse
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        $donation->update([
            'donated_at' => $request->input('donated_at'),
            'method_id' => $request->integer('method_id'),
            'value' => $request->input('value') * 100,
            'comments' => $request->input('comments'),
        ]);

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
