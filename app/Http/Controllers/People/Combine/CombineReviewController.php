<?php

namespace App\Http\Controllers\People\Combine;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\Person;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CombineReviewController extends Controller
{
    public function __invoke(Request $request, Person $person): Response
    {
        $person->validateOwnership(Auth::user()->current_team_id);

        OptionsStore::add([
            new LocaleOptions,
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
            ]),
        ]);

        $request->validate([
            'fields' => 'required|array',
        ], [
            'fields.required' => __('At least one field must be selected to search.'),
        ]);

        $query = Person::where('team_id', Auth::user()->current_team_id);

        foreach ($request->fields as $field) {
            $column = Str::after($field, '.');
            $query->where($field, $person->getRawOriginal($column));
        }

        $people = $query->orderBy('id')->get();

        return Inertia::render('People/Combine/Review', compact('people'));
    }
}
