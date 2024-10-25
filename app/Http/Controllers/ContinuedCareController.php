<?php

namespace App\Http\Controllers;

use App\Concerns\GetsCareLogs;
use App\Enums\AttributeOptionName;
use App\Enums\SettingKey;
use App\Events\PatientUpdated;
use App\Models\AttributeOption;
use App\Models\AttributeOptionUiBehavior;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use App\Support\Wrmd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ContinuedCareController extends Controller
{
    use GetsCareLogs;

    /**
     * Show the form for updating the continued care.
     */
    public function __invoke(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        if (! ($teamIsInPossession = $admission->patient->team_possession_id === Auth::user()->current_team_id)) {
            $admission->load('patient.possession');
        }

        OptionsStore::add([
            new LocaleOptions(),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionName::PATIENT_RELEASE_TYPES->value,
                AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
                AttributeOptionName::EXAM_WEIGHT_UNITS->value,
                AttributeOptionName::EXAM_TEMPERATURE_UNITS->value
            ])
        ]);

        if (Wrmd::settings(SettingKey::SHOW_TAGS)) {
            OptionsStore::add([
                'clinicalClassifications' => Options::arrayToSelectable(app(ClinicalClassifications::class)::terms()),
                'categorizationOfClinicalSigns' => Options::arrayToSelectable(app(CategorizationOfClinicalSigns::class)::terms()),
            ]);
        }

        return Inertia::render('Patients/Continued', [
            'attributeOptionUiBehaviors' => AttributeOptionUiBehavior::getGroupedAttributeOptions([
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
            ]),
            'teamIsInPossession' => $teamIsInPossession,
            'patient' => $admission->patient,
            'logs' => fn () => $this->getCareLogs($admission->patient, Auth::user(), (Wrmd::settings(SettingKey::LOG_ORDER) === 'desc'))
        ]);
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
