<?php

namespace App\Http\Controllers\Sharing;

use App\Http\Controllers\Controller;
use App\Jobs\ShareMedicalRecords;
use App\Models\Admission;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function store(Request $request, ?Patient $patient = null): RedirectResponse
    {
        $fields = $request->input('fields') ?: '*';

        $query = $patient instanceof Patient
            ? Admission::where(['team_id' => Auth::user()->currentTeam->id, 'patient_id' => $patient])
                ->selectColumns($fields, true)
                ->joinTables($fields)
                ->selectAdmissionKeys()
            : Admission::scopedList(Auth::user()->currentTeam->id, null, $fields, true)->limitToSelected();

        ShareMedicalRecords::dispatch(
            Auth::user()->currentTeam,
            Auth::user(),
            $query->toSql(),
            $query->getBindings(),
            $request->all(),
            $request->cookie('device-uuid'),
            'export'
        );

        return redirect()->route('patients.index');
    }
}
