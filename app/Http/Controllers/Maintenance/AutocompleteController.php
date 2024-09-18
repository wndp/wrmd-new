<?php

namespace App\Http\Controllers\Maintenance;

use App\Domain\Options;
use App\Domain\OptionsStore;
use App\Events\AccountUpdated;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AutocompleteController extends Controller
{
    /**
     * Display the paper form template settings index page.
     */
    public function index()
    {
        ExtensionNavigation::emit('maintenance');

        $autocompletes = settings('autocomplete', []);

        $autoCompleteAble = fields()->getLabels()->intersectByKeys(
            array_flip([
                'patients.city_found',
                'patients.reasons_for_admission',
                'patients.admitted_by',
                'patients.disposition_location',
                'patients.keywords',
                'exams.examiner',
                'patients.diagnosis',
                'patients.dispositioned_by',
                'people.city',
                'patients.reason_for_disposition',
                'patients.transported_by',
                'patients.address_found',
                'people.organization',
                'patients.care_by_rescuer',
                'exams.treatment',
                'exams.cns',
                'exams.integument',
                'exams.cardiopulmonary',
                'exams.head',
                'exams.hindlimb',
                'exams.musculoskeletal',
                'patients.notes_about_rescue',
            ])
        )
            ->toArray();

        OptionsStore::merge(['autoCompleteAble' => Options::arrayToSelectable($autoCompleteAble)]);

        return Inertia::render('Maintenance/Autocomplete/Index', compact('autocompletes'));
    }

    /**
     * Store paper form template in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'field' => 'required',
            'values' => 'required',
        ]);

        $autocompletes = collect(settings('autocomplete', []))
            ->push([
                'field' => $request->field,
                'values' => array_filter(preg_split('/,(\\s)*/us', $request->values)),
            ])
            ->values()
            ->toArray();

        settings()->set(['autocomplete' => $autocompletes]);

        event(new AccountUpdated(Auth::user()->currentAccount));

        return redirect()->route('maintenance.autocomplete.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  string  $field
     */
    public function update(Request $request, $field)
    {
        $request->validate([
            'field' => 'required',
            'values' => 'required',
        ]);

        $autocompletes = collect(settings('autocomplete', []));

        $row = $autocompletes->search(fn ($row) => $row['field'] === $field);

        abort_if(is_null($row), 404);

        settings()->set(['autocomplete' => $autocompletes->replace([$row => [
            'field' => $request->field,
            'values' => array_filter(preg_split('/,(\\s)*/us', $request->values)),
        ]])->values()->toArray()]);

        return redirect()->route('maintenance.autocomplete.index');
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param  string  $field
     */
    public function destroy(Request $request, $field)
    {
        $autocompletes = collect(settings('autocomplete', []));

        $row = $autocompletes->search(fn ($row) => $row['field'] === $field);

        abort_if(is_null($row), 404);

        settings()->set(['autocomplete' => $autocompletes->forget($row)->values()->toArray()]);

        return redirect()->route('maintenance.autocomplete.index');
    }
}
