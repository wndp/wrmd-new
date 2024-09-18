<?php

namespace App\Http\Controllers\Search;

use App\Domain\Analytics\AnalyticFilters;
use App\Domain\Options;
use App\Domain\OptionsStore;
use App\Domain\Searching\AdvancedSearch;
use App\Domain\Searching\AdvancedSearchOptions;
use App\Domain\Searching\SearchResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AdvancedSearchController extends Controller
{
    public function create(AdvancedSearchOptions $options): Response
    {
        $fields = fields()
            ->filterOut('selectable')
            ->groupBytable()
            ->transform(fn ($group) => $group->getLabels())
            ->toArray();

        OptionsStore::add($options, 'search');
        OptionsStore::add([
            'fields' => Options::arrayToSelectable($fields),
        ], 'search');

        $filters = new AnalyticFilters();

        return Inertia::render('Search/AdvancedCreate', [
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
            'rows' => 'required|array',
            'rows.*.field' => 'required',
            'rows.*.compoundOperator' => 'required',
            'rows.*.value' => 'required',
        ]);

        $queryResults = AdvancedSearch::run(
            Auth::user()->currentAccount,
            $request
        );

        return SearchResponse::make($queryResults);
    }
}
