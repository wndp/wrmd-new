<?php

namespace App\Reporting\Reports\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use App\Models\Prescription;
use Illuminate\Database\Eloquent\Builder;

trait ReportUtilities
{
    /**
     * Group prescriptions by their case number.
     */
    private function groupByCaseNumber(Prescription $prescription): string
    {
        $accountPatient = $prescription->patient->admissions($this->team->id);

        return $accountPatient instanceof Admission ? $accountPatient->caseNumber : 'Unknown case';
    }

    /**
     * Scope the due prescriptions.
     */
    private function scopePrescriptions(string $date): Builder
    {
        [$pendingPatientId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value
        ]);

        return Prescription::select('prescriptions.*')
            ->with('patient.admissions')
            ->join('patients', 'prescriptions.patient_id', '=', 'patients.id')
            ->join('admissions', 'patients.id', '=', 'admissions.patient_id')
            ->where('team_id', $this->team->id)
            ->where('disposition_id', $pendingPatientId)
            ->where('rx_started_at', '<=', $date)
            ->where(function ($query) use ($date) {
                $query->whereNull('rx_ended_at')->orWhere('rx_ended_at', '>=', $date);
            })
            ->with('patient.admissions');
    }
}
