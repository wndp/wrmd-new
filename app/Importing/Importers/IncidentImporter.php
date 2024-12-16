<?php

namespace App\Importing\Importers;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Importing\Contracts\Importer;
use App\Models\Incident;
use App\Models\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;

class IncidentImporter extends Importer implements SkipsEmptyRows, ToModel
{
    public function model(array $row)
    {
        [
            $hotlineStatusIsResolvedId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::HOTLINE_STATUSES->value, AttributeOptionUiBehavior::HOTLINE_STATUS_IS_RESOLVED->value],
        ]);

        $row = new Collection($row);

        $reportingParty = Person::updateOrCreate([
            'team_id' => $this->team->id,
            'first_name' => $this->composeValue('people.first_name', $row),
            'last_name' => $this->composeValue('people.last_name', $row),
            'phone' => $this->composeValue('people.phone', $row),
        ], $this->makeReportingParty($row));

        $incident = new Incident($this->makeIncident($row));

        $incident->incident_status_id = is_null($this->composeValue('incidents.resolved_at', $row))
            ? $this->composeValue('incidents.incident_status_id', $row)
            : $hotlineStatusIsResolvedId;

        $incident->number_of_animals = is_numeric($this->composeValue('incidents.number_of_animals', $row))
            ? $this->composeValue('incidents.number_of_animals', $row)
            : 1;

        //$incident->id = Str::uuid7();
        $incident->team_id = $this->team->id;
        $incident->responder_id = $reportingParty->id;

        return $incident;
    }

    public function rules(): array
    {
        return [
            '1' => Rule::in(['patrick@maatwebsite.nl']),

            // Above is alias for as it always validates in batches
            '*.1' => Rule::in(['patrick@maatwebsite.nl']),

            // Can also use callback validation rules
            '0' => function ($attribute, $value, $onFailure) {
                if ($value !== 'Patrick Brouwers') {
                    $onFailure('Name is not Patrick Brouwers');
                }
            },
        ];
    }

    /**
     * Make the reporting party data.
     */
    private function makeReportingParty(Collection $row): array
    {
        $reportingPartyData = ['no_solicitations' => true];

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
        foreach ($this->filterMappedAttributesForModel('incidents.') as $wrmdColumn => $importColumn) {
            $incidentData[str_replace('incidents.', '', $wrmdColumn)] = $this->composeValue($wrmdColumn, $importColumn, $row);
        }

        return array_filter($incidentData);
    }
}
