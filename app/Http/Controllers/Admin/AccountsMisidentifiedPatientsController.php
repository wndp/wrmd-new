<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class AccountsMisidentifiedPatientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __invoke(Team $team): Response
    {
        $misidentifiedPatients = Admission::select('admissions.*')
            ->where('team_id', $team->id)
            ->whereMisidentified()
            ->orderBy('common_name')
            ->with('patient', 'team')
            ->paginate()
            ->onEachSide(1);

        return Inertia::render('Admin/Teams/Misidentified', compact('team', 'misidentifiedPatients'));
    }
}
