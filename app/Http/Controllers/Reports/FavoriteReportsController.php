<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Support\Wrmd;

class FavoriteReportsController extends Controller
{
    /**
     * Add a report to the favorites group.
     *
     * @return void
     */
    public function store(string $namespace)
    {
        $favoriteReports = (array) Wrmd::settings('favoriteReports', []);

        Wrmd::settings()->set('favoriteReports', array_merge($favoriteReports, [$namespace]));
    }

    /**
     * Remove a report from the favorites group.
     *
     * @return void
     */
    public function destroy(string $namespace)
    {
        $favoriteReports = (array) Wrmd::settings('favoriteReports', []);

        if (($key = array_search($namespace, $favoriteReports)) !== false) {
            unset($favoriteReports[$key]);
        }

        Wrmd::settings()->set('favoriteReports', array_values($favoriteReports));
    }
}
