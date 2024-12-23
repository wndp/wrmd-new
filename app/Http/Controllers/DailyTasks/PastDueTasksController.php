<?php

namespace App\Http\Controllers\DailyTasks;

use App\Collections\DailyTasksCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PastDueTasksController extends Controller
{
    public function edit(Request $request): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        return Inertia::render('Patients/PastDueTasks', [
            'pastDueTasks' => DailyTasksCollection::make()->getPastDueForPatient($admission->patient, Auth::user()->currentTeam),
        ]);
    }
}
