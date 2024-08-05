<?php

namespace App\Http\Controllers;

use App\Domain\Patients\Patient;
use App\Jobs\PatientNotifications\ExcessiveDaysInCare;
use App\Jobs\PatientNotifications\FrozenPatient;
use App\Jobs\PatientNotifications\HasWildAlertEvent;
use App\Jobs\PatientNotifications\MultipleCollaborators;
use App\Jobs\PatientNotifications\PastDueTasks;
use App\Jobs\PatientNotifications\PatientLocked;
use App\Jobs\PatientNotifications\TasksDueToday;
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
        $account = Auth::user()->currentAccount;

        $patient->validateOwnership($account->id);

        if (Cache::has("patientNotification.{$patient->id}")) {
            return 0;
        }

        Bus::batch([
            new PatientLocked($account, $patient, Auth::user()),
            new ExcessiveDaysInCare($account, $patient, Auth::user()),
            new FrozenPatient($account, $patient, Auth::user()),
            new MultipleCollaborators($account, $patient, Auth::user()),
            new TasksDueToday($account, $patient, Auth::user()),
            new PastDueTasks($account, $patient, Auth::user()),
            new HasWildAlertEvent($account, $patient, Auth::user()),
        ])->name('Patient Notifications')->dispatch();

        Cache::put("patientNotification.{$patient->id}", 1, Carbon::now()->addMinutes(5));

        return 1;
    }
}
