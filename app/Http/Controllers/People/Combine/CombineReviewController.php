<?php

namespace App\Http\Controllers\People\Combine;

use App\Domain\Locality\LocaleOptions;
use App\Domain\OptionsStore;
use App\Domain\People\PeopleOptions;
use App\Domain\People\Person;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CombineReviewController extends Controller
{
    public function __invoke(
        Request $request,
        Person $person,
        LocaleOptions $LocaleOptions,
        PeopleOptions $PeopleOptions
    ): Response {
        OptionsStore::merge($LocaleOptions);
        OptionsStore::merge($PeopleOptions);

        $person->validateOwnership(Auth::user()->current_team_id);

        $request->validate([
            'fields' => 'required|array',
        ], [
            'fields.required' => __('At least one field must be selected to search.'),
        ]);

        $query = Person::where('account_id', Auth::user()->current_team_id);

        foreach ($request->fields as $field) {
            $column = Str::after($field, '.');
            $query->where($field, $person->getRawOriginal($column));
        }

        $people = $query->get();

        return Inertia::render('People/Combine/Review', compact('people'));
    }
}
