<?php

namespace App\Http\Controllers\Maintenance;

use App\Exceptions\RecordNotOwned;
use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Transfer;
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
        abort_unless($this->transferCanUncollaborate($transfer), new RecordNotOwned);

        $admission = Admission::custody($transfer->toTeam, $transfer->patient);
        $newPatient = $transfer->patient->clone();

        Admission::where([
            'team_id' => $admission->team_id,
            'case_year' => $admission->case_year,
            'case_id' => $admission->case_id,
        ])->update(['patient_id' => $newPatient->id]);

        $transfer->clonedPatient()->associate($newPatient);
        $transfer->update(['is_collaborative' => false]);

        return redirect()->route('patients.initial.edit', ['c' => $admission->case_id, 'y' => $admission->case_year])
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Patient was un-collaborated.'));
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
            in_array(Auth::user()->current_team_id, [$transfer->to_team_id, $transfer->from_team_id]);
    }
}
