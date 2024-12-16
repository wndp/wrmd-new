<?php

namespace App\Analytics\Maps;

use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Map;
use App\Analytics\MapIntoMapMarkers;
use App\Models\Admission;

class AcquisitionLocation extends Map
{
    use HandleSeriesNames;

    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series->push(
                [
                    'name' => $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to),
                    'data' => $this->query($segment)->mapInto(MapIntoMapMarkers::class),
                ]
            );

            if ($this->filters->compare) {
                $this->series->push(
                    [
                        'name' => $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to),
                        'data' => $this->compareQuery($segment)->mapInto(MapIntoMapMarkers::class),
                    ]
                );
            }
        }
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereRaw('coordinates_found != GEOGRAPHY_POINT(0, 0)')
            ->whereNotNull('coordinates_found');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->with('patient.taxon')->get();
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('admissions.*')
            ->joinPatients()
            ->whereRaw('coordinates_found != POINT(0, 0)')
            ->whereNotNull('coordinates_found')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $query->with('patient.taxon')->get();
    }
}
