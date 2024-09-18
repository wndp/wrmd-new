<?php

namespace App\Http\Controllers\People\Combine;

use App\Domain\People\Person;
use App\Events\NewPersonCreated;
use App\Http\Controllers\Controller;
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
            'oldPeople.*' => 'numeric',
        ], [
            'newPerson.required' => 'There was a problem creating the new person.',
            'newPerson.array' => 'There was a problem creating the new person.',
            'oldPeople.*.numeric' => 'There was a problem with one of the duplicate persons.',
        ]);

        DB::beginTransaction();

        try {
            event(new NewPersonCreated(
                $this->makeNewPerson($request->input('newPerson')),
                $request->input('oldPeople')
            ));

            $this->destroyOldPeople($request->input('oldPeople'));

            DB::commit();

            return redirect()->route('people.combine.search')
                ->with('flash.notificationHeading', __('Success!'))
                ->with('flash.notification', __('The duplicate people were merged into one person.'));
        } catch (QueryExceptionx $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back()
                ->with('flash.notificationHeading', __('Oops!'))
                ->with('flash.notification', __('There was an unexpected error when trying to merge the people. You may try again or click the Help link above.'))
                ->with('flash.style', 'danger');
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

        $newPerson->account()->associate(Auth::user()->currentAccount);
        $newPerson->save();

        return $newPerson;
    }

    /**
     * Permanently destroy the old people in storage.
     */
    private function destroyOldPeople(array $oldPeople): void
    {
        foreach ($oldPeople as $id) {
            optional(Person::find($id))->forceDelete();
        }
    }
}
