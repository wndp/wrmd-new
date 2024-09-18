<?php

namespace App\Http\Controllers\Reports;

use App\Reporting\FacilitatesReporting;
use App\Reporting\ReportsCollection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReportsController extends Controller
{
    use FacilitatesReporting;

    public function index(): Response
    {
        $reportsCollection = ReportsCollection::register()->initializeAll(
            Auth::user()->currentTeam
        );

        $favoriteReports = $reportsCollection->pluckFavorites();

        return Inertia::render('Reports/Index', compact('reportsCollection', 'favoriteReports'));
    }

    /**
     * Display the view to preview the report.
     */
    public function show(string $key): Response
    {
        $report = $this->reportFromKey($key);

        abort_if(is_null($report), 404);

        $report = new $report(Auth::user()->currentTeam);

        return Inertia::render('Reports/Show', compact('report'));
    }
}
