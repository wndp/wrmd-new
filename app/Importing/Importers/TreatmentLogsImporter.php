<?php

namespace App\Importing\Importers;

use App\Domain\Importing\Contracts\Importer;
use App\Domain\Patients\TreatmentLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class TreatmentLogsImporter extends Importer implements ToModel, WithBatchInserts
{
    /**
     * {@inheritdoc}
     */
    public function model(array $row)
    {
        $row = new Collection($row);

        try {
            $patient = $this->findPatientOrFail($row);
        } catch (ModelNotFoundException $e) {
            return null;
        }

        $treatmentLogData = [];
        foreach ($this->filterMappedAttributesForModel('treatment_logs.') as $wrmdColumn => $importColumn) {
            $treatmentLogData[str_replace('treatment_logs.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return tap(new TreatmentLog($treatmentLogData), function ($treatmentLog) use ($patient) {
            $treatmentLog->user_id = $this->user->id;
            $treatmentLog->patient_id = $patient->id;
        });
    }
}
