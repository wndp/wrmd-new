<?php

namespace App\Http\Controllers\Analytics;

use App\Analytics\AnalyticFiltersStore;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnalyticsFiltersController extends Controller
{
    /**
     * Update the date range to show analytics within.
     *
     * @return void
     */
    public function update(Request $request)
    {
        foreach ($request->only([
            'segments',
            'date_period',
            'date_from',
            'date_to',
            'compare',
            'compare_period',
            'compare_date_from',
            'compare_date_to',
            'group_by_period',
            'limit_to_search',
        ]) as $key => $value) {
            AnalyticFiltersStore::update($key, $value);
        }

        return back();
    }
}
