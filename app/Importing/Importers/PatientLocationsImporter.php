<?php

namespace App\Importing\Importers;

use App\Domain\Importing\Contracts\Importer;
use App\Domain\Patients\PatientLocation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class PatientLocationsImporter extends Importer implements ToModel, WithBatchInserts
{
    /**
     * {@inheritdoc}
     */
    public function model(array $row)
    {
        $row = new Collection($row);

        try {
            $patient = $this->findPatientOrFail($row);
        } catch (\Throwable $e) {
            return null;
        }

        $patientLocationData = [];
        foreach ($this->filterMappedAttributesForModel('patient_locations.') as $wrmdColumn => $importColumn) {
            $patientLocationData[str_replace('patient_locations.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        $patientLocationData['moved_in_at'] = $this->composeValue('patient_locations.moved_in_at', $row);

        return tap(new PatientLocation($patientLocationData), function ($patientLocation) use ($patient) {
            $patientLocation->patient_id = $patient->id;
        });
    }
}
