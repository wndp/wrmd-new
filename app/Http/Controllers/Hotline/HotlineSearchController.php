<?php

namespace App\Http\Controllers\Hotline;

use App\Actions\IncidentSearch;
use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Options\LocaleOptions;
use App\Repositories\IncidentRepository;
use App\Repositories\OptionsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class HotlineSearchController extends Controller
{
    /**
     * Return the view to search hotline records.
     */
    public function create(): Response
    {
        OptionsStore::add([
            new LocaleOptions(),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
                AttributeOptionName::HOTLINE_WILDLIFE_CATEGORIES->value,
                AttributeOptionName::HOTLINE_ADMINISTRATIVE_CATEGORIES->value,
                AttributeOptionName::HOTLINE_OTHER_CATEGORIES->value,
                AttributeOptionName::HOTLINE_STATUSES->value,
            ])
        ]);

        return Inertia::render('Hotline/Search/Create');
    }

    /**
     * Return a list of the searched hotline records.
     */
    public function search(Request $request): Response
    {
        $incidents = IncidentSearch::run(Auth::user()->currentTeam, $request);

        return Inertia::render('Hotline/Search/Index', compact('incidents'));
    }
}
