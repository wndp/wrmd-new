<?php

namespace App\Analytics\Tables;

use App\Analytics\Contracts\Table;
use App\Analytics\MapIntoDataTableRow;
use App\Analytics\Series;

class PatientsByEndemicStatuses extends Table
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->series = new Series();

        foreach ($this->filters->segments as $segment) {
            $this->series = $this->series->merge(
                $this->query($segment)
                    ->map(function ($admission) {
                        $admission->group = in_array('US-CA', json_decode($admission->native_distributions) ?? []) ? 'Native' : 'Non-native';

                        return $admission;
                    })
                    ->filter(function ($admission) {
                        return ! is_null($admission->group);
                    })
                    ->groupBy('group')
                    ->sortKeys()
                    ->mapInto(MapIntoDataTableRow::class)
                    ->map(function ($mapIntoDataTableRow) use ($segment) {
                        $mapIntoDataTableRow->buildSeriesName($segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to);

                        return $mapIntoDataTableRow;
                    })
                    ->values()
            );

            if ($this->filters->compare) {
                $this->series = $this->series->merge(
                    $this->compareQuery($segment)
                        ->map(function ($admission) {
                            $admission->group = in_array('US-CA', json_decode($admission->native_distributions) ?? []) ? 'Native' : 'Non-native';

                            return $admission;
                        })
                        ->filter(function ($admission) {
                            return ! is_null($admission->group);
                        })
                        ->groupBy('group')
                        ->sortKeys()
                        ->mapInto(MapIntoDataTableRow::class)
                        ->map(function ($mapIntoDataTableRow) use ($segment) {
                            $mapIntoDataTableRow->buildSeriesName($segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to);

                            return $mapIntoDataTableRow;
                        })
                        ->values()
                );
            }
        }
    }

    public function query($segment)
    {
        return $this->baseQuery()
            ->addSelect('native_distributions')
            ->joinTaxa()
            ->withSegment($segment)
            ->get();
    }

    public function compareQuery($segment)
    {
        return $this->baseCompareQuery()
            ->addSelect('native_distributions')
            ->joinTaxa()
            ->withSegment($segment)
            ->get();
    }
}
