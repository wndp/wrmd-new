<?php

namespace App\Http\Controllers\Search;

use App\Domain\Analytics\AnalyticFilters;
use App\Domain\Locality\LocaleOptions;
use App\Domain\OptionsStore;
use App\Domain\Patients\ExamOptions;
use App\Domain\People\PeopleOptions;
use App\Domain\Searching\SearchResponse;
use App\Domain\Searching\SimpleSearch;
use App\Domain\Taxonomy\TaxonomyOptions;
use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function create(
        ExamOptions $examOptions,
        LocaleOptions $localeOptions,
        TaxonomyOptions $taxonomyOptions,
        PeopleOptions $peopleOptions,
        $tab = null
    ): Response {
        OptionsStore::add($examOptions);
        OptionsStore::add($localeOptions);
        OptionsStore::add($taxonomyOptions);
        OptionsStore::add($peopleOptions);
        ExtensionNavigation::emit('searching');

        $filters = new AnalyticFilters;

        return Inertia::render('Search/Create', [
            'datePeriod' => $filters->defaultDatePeriod(),
            'dateFrom' => $filters->defaultDateFrom(),
            'dateTo' => $filters->defaultDateTo(),
        ]);
    }

    /**
     * Search for the requested patients.
     */
    public function search(Request $request): SearchResponse
    {
        $request->validate([
            'admitted_at_from' => 'required|date',
            'admitted_at_to' => 'required|date',
        ]);

        $queryResults = SimpleSearch::run(
            Auth::user()->currentAccount,
            $request
        );

        return SearchResponse::make($queryResults);
    }
}
