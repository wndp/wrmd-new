<?php

namespace App\Http\Controllers;

use App\Concerns\RetrievesTreatmentLogs;
use App\Domain\Admissions\Admission;
use App\Domain\Classifications\CategorizationOfClinicalSigns;
use App\Domain\Classifications\ClinicalClassifications;
use App\Domain\Locality\LocaleOptions;
use App\Domain\Options;
use App\Domain\OptionsStore;
use App\Domain\Patients\ExamOptions;
use App\Domain\Patients\TreatmentLog;
use App\Events\PatientUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ContinuedCareController extends Controller
{
    use RetrievesTreatmentLogs;

    /**
     * Show the form for updating the continued care.
     */
    public function __invoke(
        ExamOptions $examOptions,
        LocaleOptions $localeOptions,
    ): Response {
        OptionsStore::merge($examOptions);
        OptionsStore::merge($localeOptions);

        if (settings('showTags')) {
            OptionsStore::merge([
                'clinicalClassifications' => Options::arrayToSelectable(app(ClinicalClassifications::class)::terms()),
                'categorizationOfClinicalSigns' => Options::arrayToSelectable(app(CategorizationOfClinicalSigns::class)::terms()),
            ]);
        }

        $admission = $this->loadAdmissionAndSharePagination();
        $logs = $this->getTreatmentLogs($admission->patient, Auth::user(), (settings('logOrder') === 'desc'));

        return Inertia::render('Patients/Continued', compact(
            'logs'
        ));
    }

    /**
     * Update the specified continued care in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $caseYear
     * @param  int  $caseId
     * @return \Illuminate\Http\Response
     */
    // public function update(Request $request, $caseYear, $caseId)
    // {
    //     $admission = Admission::owner(Auth::user()->currentAccount->id, $caseYear, $caseId)->first();

    //     $request->merge([
    //         'criminal_activity' => $request->criminal_activity ?: 0,
    //         'carcass_saved' => $request->carcass_saved ?: 0,
    //     ]);

    //     if ($request->offsetExists('disposition_lat')) {
    //         $admission->patient::$disableGeoLocation = true;
    //         $admission->patient->disposition_coordinates = new Point($request->disposition_lat, $request->disposition_lng);
    //         $admission->patient->save();
    //     }

    //     $admission->patient->update($request->all());

    //     $this->saveTreatmentLog($request, $admission->patient_id);
    //     $this->saveRecheck($request, $admission->patient_id);

    //     event(new PatientUpdated($admission->patient));

    //     flash()->success('Continued care updated.');

    //     return redirect()->route('continued.edit', ['y' => $caseYear, 'c' => $caseId]);
    // }

    // protected function saveTreatmentLog($request, $patientId)
    // {
    //     $treatmentLogData = collect($request->get('treatment_log'));

    //     $treatmentLogValidator = Validator::make($treatmentLogData->toArray(), [
    //         'treated_at' => 'required|date',
    //         'weight' => 'nullable|required_without_all:comments|numeric',
    //         'comments' => 'nullable|required_without_all:weight',
    //     ]);

    //     if ($treatmentLogValidator->passes()) {
    //         TreatmentLog::store($patientId, $treatmentLogData, auth()->user());
    //     }
    // }

    // protected function saveRecheck($request, $patientId)
    // {
    //     $recheckData = collect($request->get('recheck'));

    //     $recheckValidator = Validator::make($recheckData->toArray(), [
    //         'scheduled_at' => 'required|date',
    //         'assigned_to' => 'required',
    //         'description' => 'required',
    //     ]);

    //     if ($recheckValidator->passes()) {
    //         Recheck::store($patientId, $recheckData);
    //     }
    // }
}
