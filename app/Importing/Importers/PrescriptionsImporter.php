<?php

namespace App\Importing\Importers;

use App\Domain\DailyTasks\Prescriptions\Prescription;
use App\Domain\Importing\Contracts\Importer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class PrescriptionsImporter extends Importer implements ToModel, WithBatchInserts
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

        $data = [];
        foreach ($this->filterMappedAttributesForModel('prescriptions.') as $wrmdColumn => $importColumn) {
            $data[str_replace('prescriptions.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return tap(new Prescription($data), function ($prescription) use ($patient) {
            $prescription->patient_id = $patient->id;
        });
    }
}
