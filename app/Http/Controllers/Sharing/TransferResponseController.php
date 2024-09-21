<?php

namespace App\Http\Controllers\Sharing;

use App\Events\PatientAdmitted;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Transfer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TransferResponseController extends Controller
{
    /**
     * Accept a transfer request.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Transfer $transfer)
    {
        // Only the recipient team can accept a transfer request
        if (! $transfer->toTeam->is(Auth::user()->currentTeam)) {
            return $this->badRequestResponse(__('Your team can not accept this transfer request.'));
        }

        // Transfers can not be accepted if they have already been responded to.
        if (! is_null($transfer->responded_at)) {
            return $this->badRequestResponse(__('This transfer request has already be responded to.'));
        }

        // Teams can not accept a collaborative patient they already have admitted.
        if ($this->isCollaborativePatientAlreadyAdmitted($transfer)) {
            return $this->badRequestResponse(__('This patient has already been admitted to your team.'));
        }

        $rootAdmission = Admission::custody($transfer->fromTeam, $transfer->patient);
        $newPatient = $transfer->is_collaborative ? $rootAdmission->patient : $rootAdmission->patient->clone();

        $newAdmission = tap(new Admission(['case_year' => date('Y')]), function ($newAdmission) use ($transfer, $newPatient) {
            $newAdmission->team()->associate($transfer->toTeam);
            $newAdmission->patient()->associate($newPatient);
            $newAdmission->save();
        });

        $newPatient->team_possession_id = $transfer->toTeam->id;
        $newPatient->save();

        if (! $transfer->is_collaborative) {
            $transfer->clonedPatient()->associate($newPatient);
        }

        $transfer->accept();
        event(new PatientAdmitted($transfer->toTeam, $newAdmission->patientPromise()));

        return redirect()->route('patients.initial.edit', ['c' => $newAdmission->case_id, 'y' => $newAdmission->case_year])
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Patient transferred to your team.'));
    }

    /**
     * Deny a transfer request.
     */
    public function destroy(Transfer $transfer): RedirectResponse
    {
        $transfer->deny();

        return redirect()->route('maintenance.transfers')
            ->with('notification.heading', __('Heads Up!'))
            ->with('notification.text', __('Patient was not transferred to your team.'));
    }

    public function badRequestResponse($message): RedirectResponse
    {
        return redirect()->route('maintenance.transfers')
            ->with('notification.heading', __('Oops!'))
            ->with('notification.text', $message)
            ->with('notification.style', 'danger');
    }

    /**
     * Determine if a patient is already admitted by an team.
     */
    private function isCollaborativePatientAlreadyAdmitted(Transfer $transfer): bool
    {
        return $transfer->is_collaborative && ! is_null(
            Admission::custody($transfer->toTeam, $transfer->patient)
        );
    }
}
