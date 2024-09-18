<?php

namespace App\Http\Controllers\Maintenance;

use App\Extensions\ExtensionNavigation;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UnrecognizedPatientsController extends Controller
{
    /**
     * Display the maintenance index page.
     */
    public function __invoke(): Response
    {
        //ExtensionNavigation::emit('maintenance');

        $admissions = Admission::select('admissions.*')
            ->where('team_id', Auth::user()->current_team_id)
            ->whereUnrecognized()
            ->orderBy('common_name')
            ->with('patient', 'team')
            ->paginate()
            ->onEachSide(1);

        return Inertia::render('Maintenance/UnrecognizedPatients', compact('admissions'));
    }
}
