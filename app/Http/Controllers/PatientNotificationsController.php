<?php

namespace App\Http\Controllers;

use App\Jobs\PatientNotifications\ExcessiveDaysInCare;
use App\Jobs\PatientNotifications\HasWildAlertEvent;
use App\Jobs\PatientNotifications\MultipleCollaborators;
use App\Jobs\PatientNotifications\PastDueTasks;
use App\Jobs\PatientNotifications\PatientInUse;
use App\Jobs\PatientNotifications\PatientLocked;
use App\Jobs\PatientNotifications\TasksDueToday;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class PatientNotificationsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Patient $patient)
    {
        $team = Auth::user()->currentTeam;

        $patient->validateOwnership($team->id);

        if (Cache::has("patientNotification.{$patient->id}")) {
            return 0;
        }

        Bus::batch([
            //new PatientInUse($team, $patient, Auth::user()),
            new ExcessiveDaysInCare($team, $patient, Auth::user()),
            new PatientLocked($team, $patient, Auth::user()),
            new MultipleCollaborators($team, $patient, Auth::user()),
            new TasksDueToday($team, $patient, Auth::user()),
            new PastDueTasks($team, $patient, Auth::user()),
            new HasWildAlertEvent($team, $patient, Auth::user()),
        ])->name('Patient Notifications')->dispatch();

        Cache::put("patientNotification.{$patient->id}", 1, Carbon::now()->addMinutes(5));

        return 1;
    }
}
