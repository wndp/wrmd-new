<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Team;
use Inertia\Inertia;
use Inertia\Response;

class AccountsUnrecognizedPatientsController extends Controller
{
    public function __invoke(Team $team): Response
    {
        $unrecognizedPatients = Admission::select('admissions.*')
            ->where('team_id', $team->id)
            ->whereUnrecognized()
            ->orderBy('common_name')
            ->with('patient', 'team')
            ->paginate()
            ->onEachSide(1);

        return Inertia::render('Admin/Teams/Unrecognized', compact('team', 'unrecognizedPatients'));
    }
}
