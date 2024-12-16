<?php

namespace App\Analytics\Maps;

use App\Analytics\Contracts\Map;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class DispositionLocation extends Map
{
    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series->push(
                [
                    'name' => $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                    'data' => $this->query($segment)->map(function ($admission) {
                        return [
                            'coordinates' => [
                                'lat' => $admission->patient->disposition_coordinates?->latitude,
                                'lng' => $admission->patient->disposition_coordinates?->longitude,
                            ],
                            'title' => "{$admission->case_number} {$admission->patient->common_name}",
                            'content' => $admission->patient->disposition_address,
                            'link' => $admission->href,
                        ];
                    }),
                ]
            );

            if ($this->filters->compare) {
                $this->series->push(
                    [
                        'name' => $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                        'data' => $this->compareQuery($segment)->map(function ($admission) {
                            return [
                                'coordinates' => [
                                    'lat' => $admission->patient->disposition_coordinates?->latitude,
                                    'lng' => $admission->patient->disposition_coordinates?->longitude,
                                ],
                                'title' => "{$admission->case_number} {$admission->patient->common_name}",
                                'content' => $admission->patient->disposition_address,
                                'link' => $admission->href,
                            ];
                        }),
                    ]
                );
            }
        }
    }

    public function query($segment)
    {
        $releasedAndTransferredIds = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value],
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereRaw('disposition_coordinates != GEOGRAPHY_POINT(0, 0)')
            ->whereNotNull('disposition_coordinates')
            ->whereNotNull('disposition_address')
            ->whereIn('disposition_id', $releasedAndTransferredIds);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->with('patient.species')->get();
    }

    public function compareQuery($segment)
    {
        $releasedAndTransferredIds = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value],
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereRaw('disposition_coordinates != GEOGRAPHY_POINT(0, 0)')
            ->whereNotNull('disposition_coordinates')
            ->whereNotNull('disposition_address')
            ->whereIn('disposition_id', $releasedAndTransferredIds)
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $query->with('patient.species')->get();
    }
}
