<?php

namespace App\Http\Controllers\Analytics;

use App\Domain\Analytics\AnalyticFilters;
use App\Domain\Analytics\AnalyticFiltersStore;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(): Response
    {
        //AnalyticFiltersStore::destroy();

        $filters = new AnalyticFilters();
        $datePeriod = $filters->defaultDatePeriod();
        $dateFrom = $filters->defaultDateFrom();
        $dateTo = $filters->defaultDateTo();
        $segment = $filters->defaultSegment();

        return Inertia::render('Analytics/Pages/Patients/Overview', compact('datePeriod', 'dateFrom', 'dateTo', 'segment'));
    }
}
