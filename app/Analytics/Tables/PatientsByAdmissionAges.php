<?php

namespace App\Analytics\Tables;

use App\Analytics\Contracts\Table;
use App\Analytics\MapIntoDataTableRow;

class PatientsByAdmissionAges extends Table
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series = $this->series->merge(
                $this->query($segment)
                    ->groupBy('age_unit')
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
                        ->groupBy('age_unit')
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
            ->addSelect('age_unit')
            ->joinIntakeExam()
            ->whereNotNull('age_unit')
            ->withSegment($segment)
            ->get();
    }

    public function compareQuery($segment)
    {
        return $this->baseCompareQuery()
            ->addSelect('age_unit')
            ->joinIntakeExam()
            ->whereNotNull('age_unit')
            ->withSegment($segment)
            ->get();
    }
}
