<?php

namespace App\Http\Controllers\Oiled;

use App\Domain\OptionsStore;
use App\Domain\Patients\Patient;
use App\Extensions\EventConditioning\Conditioning;
use App\Extensions\EventConditioning\ConditioningOptions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ConditioningController extends Controller
{
    public function index(ConditioningOptions $options)
    {
        OptionsStore::merge($options);

        $this->loadAdmissionAndSharePagination()->patient->load('eventConditioning');

        return Inertia::render('Oiled/Conditioning');
    }

    public function store(Request $request, Patient $patient)
    {
        $patient->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'evaluated_at' => 'required|date',
            'examiner' => 'required',
        ], [
            'evaluated_at.required' => 'The evaluated at date field is required.',
            'evaluated_at.date' => 'The evaluated at date is not a valid date.',
        ]);

        tap(new Conditioning($this->formatRequestInput($request)), function ($wash) use ($patient) {
            $wash->patient()->associate($patient);
            $wash->save();
        });

        return back();
    }

    /**
     * Update a conditioning in storage.
     */
    public function update(Request $request, Patient $patient, Conditioning $conditioning)
    {
        $conditioning->validateOwnership(Auth::user()->current_account_id);

        $request->validate([
            'evaluated_at' => 'required|date',
            'examiner' => 'required',
        ], [
            'evaluated_at.required' => 'The evaluated at date field is required.',
            'evaluated_at.date' => 'The evaluated at date is not a valid date.',
        ]);

        $conditioning->update($this->formatRequestInput($request));

        return back();
    }

    /**
     * Delete conditioning from storage.
    */
    public function destroy(Request $request, Patient $patient, Conditioning $conditioning)
    {
        $conditioning->validateOwnership(Auth::user()->current_account_id);

        $conditioning->delete();

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
            'is_self_feeding' => $request->is_self_feeding === 'Unknown' ? null : ($request->is_self_feeding === 'Yes' ? 1 : 0),
            'is_flighted' => $request->is_flighted === 'Unknown' ? null : ($request->is_flighted === 'Yes' ? 1 : 0),
            'evaluated_at' => $request->convertDateFromLocal('evaluated_at'),
        ]);
    }
}
