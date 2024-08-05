<?php

namespace App\Importing\Importers;

use App\Domain\Importing\Contracts\Importer;
use App\Domain\Patients\Exam;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ExamImporter extends Importer implements ToModel, WithBatchInserts
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

        $examData = [];
        foreach ($this->filterMappedAttributesForModel('exams.') as $wrmdColumn => $importColumn) {
            $examData[str_replace('exams.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return tap(new Exam($examData), function ($exam) use ($patient, $examData) {
            $exam->examined_at = $examData['examined_at'] ?? $patient->date_admitted_at;
            $exam->type = 'Daily';
            $exam->patient_id = $patient->id;
        });
    }
}
