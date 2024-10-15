<?php

namespace App\Http\Controllers\People\Combine;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Incident;
use App\Models\Patient;
use App\Models\Person;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CombineMergeController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'newPerson' => 'required|array',
            'oldPeople' => 'required|array',
            'oldPeople.*' => 'string',
        ], [
            'newPerson.required' => 'There was a problem creating the new person.',
            'newPerson.array' => 'There was a problem creating the new person.',
            'oldPeople.*.string' => 'There was a problem with one of the duplicate persons.',
        ]);

        DB::beginTransaction();

        try {
            $newPerson = $this->makeNewPerson($request->input('newPerson'));

            $this->reAssociatePatients($newPerson, $request->input('oldPeople'));
            $this->reAssociateIncidents($newPerson, $request->input('oldPeople'));
            $this->reAssociateDonations($newPerson, $request->input('oldPeople'));
            $this->destroyOldPeople($request->input('oldPeople'));

            DB::commit();

            return redirect()->route('people.combine.search')
                ->with('notification.heading', __('Success!'))
                ->with('notification.text', __('The duplicate people were merged into one person.'));
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back()
                ->with('notification.heading', __('Oops!'))
                ->with('notification.text', __('There was an unexpected error when trying to merge the people. You may try again or click the Help link above.'))
                ->with('notification.style', 'danger');
        }
    }

    /**
     * Create the new person.
     */
    private function makeNewPerson(array $attributes): Person
    {
        $newPerson = new Person(array_merge($attributes, [
            'no_solicitations' => isset($attributes['no_solicitations']) ?: 0,
            'is_volunteer' => isset($attributes['is_volunteer']) ?: 0,
            'is_member' => isset($attributes['is_member']) ?: 0,
        ]));

        $newPerson->team()->associate(Auth::user()->currentTeam);
        $newPerson->save();

        return $newPerson;
    }

    private function reAssociatePatients(Person $newPerson, array $oldPeople)
    {
        Patient::whereIn('rescuer_id', $oldPeople)->update(['rescuer_id' => $newPerson->id]);
    }

    private function reAssociateIncidents(Person $newPerson, array $oldPeople)
    {
        Incident::withTrashed()->whereIn('responder_id', $oldPeople)->update(['responder_id' => $newPerson->id]);
    }

    private function reAssociateDonations(Person $newPerson, array $oldPeople)
    {
        Donation::whereIn('person_id', $oldPeople)->update(['person_id' => $newPerson->id]);
    }

    private function destroyOldPeople(array $oldPeople): void
    {
        foreach ($oldPeople as $id) {
            optional(Person::find($id))->forceDelete();
        }
    }
}
