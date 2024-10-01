<?php

namespace App\Http\Controllers\Oiled;

use App\Domain\OptionsStore;
use App\Domain\Patients\Patient;
use App\Extensions\EventWash\Wash;
use App\Extensions\EventWash\WashOptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WashController extends Controller
{
    public function index(WashOptions $options): Response
    {
        OptionsStore::merge($options);

        $this->loadAdmissionAndSharePagination()->patient->load('eventWash');

        return Inertia::render('Oiled/Wash');
    }

    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'washed_at' => 'required|date',
        ], [
            'washed_at.required' => 'The washed at date field is required.',
            'washed_at.date' => 'The washed at date is not a valid date.',
        ]);

        tap(new Wash($this->formatRequestInput($request)), function ($wash) use ($patient) {
            $wash->patient()->associate($patient);
            $wash->save();
        });

        return back();
    }

    /**
     * Update a wash in storage.
     */
    public function update(Request $request, Patient $patient, Wash $wash)
    {
        $wash->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'washed_at' => 'required|date',
        ], [
            'washed_at.required' => 'The washed at date field is required.',
            'washed_at.date' => 'The washed at date is not a valid date.',
        ]);

        $wash->update($this->formatRequestInput($request));

        return back();
    }

    /**
     * Delete wash from storage.
    */
    public function destroy(Request $request, Patient $patient, Wash $wash)
    {
        $wash->validateOwnership(Auth::user()->current_account_id);

        $wash->delete();

        return back();
    }

    /**
     * Format the request input for safe database storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function formatRequestInput($request)
    {
        return array_merge($request->all(), [
            'washed_at' => $request->convertDateFromLocal('washed_at'),
        ]);
    }
}
