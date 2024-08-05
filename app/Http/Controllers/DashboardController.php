<?php

namespace App\Http\Controllers;

use App\Analytics\AnalyticFiltersStore;
use App\Repositories\RecentNews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        AnalyticFiltersStore::destroy();

        $recentNews = RecentNews::collect()->take(3);
        $currentYear = (int) session('caseYear') === (int) now()->format('Y');

        $analyticFiltersForThisYear = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::createFromDate(session('caseYear'))->startOfYear()->format('Y-m-d'),
            'date_to' => $currentYear
                ? Carbon::createFromDate(session('caseYear'))->format('Y-m-d')
                : Carbon::createFromDate(session('caseYear'))->endOfYear()->format('Y-m-d'),
            'compare' => true,
            'compare_date_from' => Carbon::createFromDate(session('caseYear') - 1)->startOfYear()->format('Y-m-d'),
            'compare_date_to' => $currentYear
                ? Carbon::createFromDate(session('caseYear') - 1)->format('Y-m-d')
                : Carbon::createFromDate(session('caseYear') - 1)->endOfYear()->format('Y-m-d'),
            'group_by_period' => 'day',
        ];

        $analyticFiltersForThisWeek = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'compare' => true,
            'compare_date_from' => Carbon::now()->startOfWeek()->subDays(7)->format('Y-m-d'),
            'compare_date_to' => Carbon::now()->subDays(7)->format('Y-m-d'),
            'group_by_period' => 'day',
        ];

        return Inertia::render('Dashboard', compact('recentNews', 'analyticFiltersForThisYear', 'analyticFiltersForThisWeek'));
    }
}
