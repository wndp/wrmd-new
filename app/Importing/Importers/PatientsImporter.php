<?php

namespace App\Importing\Importers;

use App\Domain\Admissions\AdmitPatient;
use App\Domain\Importing\Contracts\Importer;
use App\Domain\Patients\Exam;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class PatientsImporter extends Importer implements OnEachRow
{
    public function onRow(Row $row)
    {
        $row = $row->toCollection();

        try {
            // Patient
            $patientData = [];
            foreach ($this->filterMappedAttributesForModel('patients.') as $wrmdColumn => $importColumn) {
                $patientData[str_replace('patients.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
            }

            if (! $this->isMapped('patients.found_at') || ! $this->composeValue('patients.found_at', $row)) {
                $patientData['found_at'] = $this->composeValue('patients.date_admitted_at', $row)->format('Y-m-d');
            }

            $patientData = array_filter($patientData);

            // Rescuer
            $rescuerData = [];
            foreach ($this->filterMappedAttributesForModel('people.') as $wrmdColumn => $importColumn) {
                $rescuerData[str_replace('people.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
            }
            $rescuerData = array_filter($rescuerData);

            $admissions = AdmitPatient::casesToCreate(1)->withoutEvents()->process(
                $this->account,
                $this->composeValue('patients.date_admitted_at', $row)->format('Y'),
                $rescuerData,
                $patientData
            )->each(function ($admission) use ($row) {
                $admission->patientPromise()->update([
                    'disposition' => $this->composeValue('patients.disposition', $row),
                ]);
            });

            // Initial Exam
            $examData = [];
            foreach ($this->filterMappedAttributesForModel('exams.') as $wrmdColumn => $importColumn) {
                $examData[str_replace('exams.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
            }
            $examData = array_filter($examData);

            if (! empty($examData)) {
                $examData['examined_at'] = $examData['examined_at'] ?? $this->composeValue('patients.date_admitted_at', $row);
                Exam::getIntakeExam($admissions->first()->patientPromise()->id)->fill($examData)->save();
            }
        } catch (\Throwable $e) {
            $this->logFailedImport($row, $e);
        }
    }
}
