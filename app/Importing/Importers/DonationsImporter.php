<?php

namespace App\Importing\Importers;

use App\Domain\Importing\Contracts\Importer;
use App\Domain\People\Donation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class DonationsImporter extends Importer implements ToModel, WithBatchInserts
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

        $data = [];
        foreach ($this->filterMappedAttributesForModel('donations.') as $wrmdColumn => $importColumn) {
            $data[str_replace('donations.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return tap(new Donation($data), function ($donation) use ($patient) {
            $donation->person_id = $patient->rescuer_id;
        });
    }
}
