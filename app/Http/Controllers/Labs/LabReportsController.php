<?php

namespace App\Http\Controllers\Labs;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Models\LabReport;
use App\Models\Patient;
use App\Repositories\OptionsStore;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LabReportsController extends Controller
{
    public function index(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::LAB_BOOLEAN->value,
                AttributeOptionName::LAB_PLASMA_COLORS->value,
                AttributeOptionName::LAB_RESULT_QUANTITY_UNITS->value,
                AttributeOptionName::LAB_CHEMISTRY_UNITS->value,
                AttributeOptionName::LAB_URINE_COLLECTION_METHODS->value,
                AttributeOptionName::LAB_URINE_TURBIDITIES->value,
                AttributeOptionName::LAB_URINE_ODORS->value,
                AttributeOptionName::LAB_TOXINS->value,
                AttributeOptionName::LAB_TOXIN_LEVEL_UNITS->value,
            ])
        ]);

        return Inertia::render('Patients/Labs/Index', [
            'patient' => $admission->patient,
            'labReports' => $admission->patient->labReports->load('labResult')->transform(fn ($labReport) => [
                'id' => $labReport->id,
                'analysis_date_at' => $labReport->analysis_date_at,
                'analysis_date_at_for_humans' => $labReport->analysis_date_at?->translatedFormat(config('wrmd.date_format')),
                'technician' => $labReport->technician,
                'accession_number'  => $labReport->accession_number,
                'analysis_facility'  => $labReport->analysis_facility,
                'comments' => $labReport->comments,
                'analysis_type' => $labReport->lab_result_type,
                'badge_text' => $labReport->labResult->badge_text,
                'badge_color' => $labReport->labResult->badge_color,
                'lab_result' => $labReport->labResult,
            ])
        ]);
    }

    public function delete(Patient $patient, LabReport $labReport)
    {
        $labReport->validateOwnership(Auth::user()->current_team_id)
            ->validateRelationshipWithPatient($patient)
            ->delete();

        return back()
            ->with('notification.heading', __('Success!'))
            ->with('notification.text', __('Your fecal analysis was created.'));
    }
}
