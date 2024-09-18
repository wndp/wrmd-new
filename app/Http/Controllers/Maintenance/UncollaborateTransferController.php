<?php

namespace App\Http\Controllers\Maintenance;

use App\Domain\Admissions\Admission;
use App\Domain\Database\RecordNotOwnedResponse;
use App\Domain\Patients\Transfer;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UncollaborateTransferController extends Controller
{
    /**
     * Display the maintenance index page.
     */
    public function __invoke(Request $request, Transfer $transfer): RedirectResponse
    {
        abort_unless($this->transferCanUncollaborate($transfer), new RecordNotOwnedResponse());

        $admission = Admission::custody($transfer->toAccount, $transfer->patient);
        $newPatient = $transfer->patient->clone();

        Admission::where([
            'account_id' => $admission->account_id,
            'case_year' => $admission->case_year,
            'case_id' => $admission->case_id,
        ])->update(['patient_id' => $newPatient->id]);

        $transfer->clonedPatient()->associate($newPatient);
        $transfer->update(['is_collaborative' => false]);

        return redirect()->route('patients.initial.edit', ['c' => $admission->case_id, 'y' => $admission->case_year])
            ->with('flash.notificationHeading', __('Success!'))
            ->with('flash.notification', __('Patient was un-collaborated.'));
    }

    /**
     * Determine if the transfer can be Uncollaborated.
     *
     * @return bool
     */
    public function transferCanUncollaborate(TRansfer $transfer)
    {
        return $transfer->is_collaborative &&
            $transfer->is_accepted &&
            in_array(Auth::user()->current_account_id, [$transfer->to_account_id, $transfer->from_account_id]);
    }
}
