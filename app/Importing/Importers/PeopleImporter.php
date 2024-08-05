<?php

namespace App\Importing\Importers;

use App\Domain\Importing\Contracts\Importer;
use App\Domain\People\Person;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class PeopleImporter extends Importer implements ToModel, WithBatchInserts
{
    /**
     * {@inheritdoc}
     */
    public function model(array $row)
    {
        $row = new Collection($row);

        $data['no_solicitations'] = true;

        foreach ($this->filterMappedAttributesForModel('people.') as $wrmdColumn => $importColumn) {
            $data[str_replace('people.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }
        $data['is_volunteer'] = $this->isMapped('people.is_volunteer') ? (bool) $this->composeValue('people.is_volunteer', $row) : false;
        $data['is_member'] = $this->isMapped('people.is_member') ? (bool) $this->composeValue('people.is_member', $row) : false;

        return tap(new Person($data), function ($person) {
            $person->team_id = $this->account->id;
        });
    }
}
