<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __invoke(): Response
    {
        $analyticFiltersForAllYears = [
            'segments' => ['All Patients'],
            'date_period' => 'all-dates',
            'date_from' => '',
            'date_to' => '',
            'compare' => false,
            'group_by_period' => 'month',
        ];

        $analyticFiltersForThisWeek = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfWeek()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'compare' => false,
        ];

        $analyticFiltersForToday = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now('America/Los_Angeles')->format('Y-m-d'),
            'date_to' => Carbon::now('America/Los_Angeles')->format('Y-m-d'),
            'compare' => false,
        ];

        return Inertia::render('Admin/Dashboard', compact(
            'analyticFiltersForAllYears',
            'analyticFiltersForThisWeek',
            'analyticFiltersForToday'
        ));
    }
}
