<?php

namespace App\Importing\Importers;

use App\Domain\Hotline\Models\Incident;
use App\Domain\Importing\Contracts\Importer;
use App\Domain\People\Person;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class IncidentImporter extends Importer implements ToModel, WithBatchInserts
{
    /**
     * {@inheritdoc}
     */
    public function model(array $row)
    {
        $row = new Collection($row);

        $reportingParty = Person::getExistingOrCreate(
            null,
            $this->account->id,
            $this->makeReportingParty($row)
        );

        tap(new Incident($this->makeIncident($row)), function ($incident) use ($reportingParty, $row) {
            $incident->status = is_null($this->composeValue('hotline_incidents.resolved_at', $row))
                ? $this->composeValue('hotline_incidents.status', $row) : 'Resolved';

            $incident->number_of_animals = is_numeric($this->composeValue('hotline_incidents.number_of_animals', $row))
                ? $this->composeValue('hotline_incidents.number_of_animals', $row) : 1;

            $incident->team_id = $this->account->id;
            $incident->responder_id = $reportingParty->id;
            $incident->save();
        });
    }

    /**
     * Make the reporting party data.
     */
    private function makeReportingParty(Collection $row): array
    {
        $reportingPartyData = [];
        foreach ($this->filterMappedAttributesForModel('people.') as $wrmdColumn => $importColumn) {
            $reportingPartyData[str_replace('people.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return array_filter($reportingPartyData);
    }

    /**
     * Make the incident data.
     */
    private function makeIncident(Collection $row): array
    {
        $incidentData = [];
        foreach ($this->filterMappedAttributesForModel('hotline_incidents.') as $wrmdColumn => $importColumn) {
            $incidentData[str_replace('hotline_incidents.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return array_filter($incidentData);
    }
}
