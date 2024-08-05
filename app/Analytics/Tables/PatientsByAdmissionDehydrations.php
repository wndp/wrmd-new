<?php

namespace App\Analytics\Tables;

use App\Analytics\Contracts\Table;
use App\Analytics\MapIntoDataTableRow;
use App\Analytics\Series;

class PatientsByAdmissionDehydrations extends Table
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
                    ->groupBy('dehydration')
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
                        ->groupBy('dehydration')
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
            ->addSelect('dehydration')
            ->joinIntakeExam()
            ->whereNotNull('dehydration')
            ->withSegment($segment)
            ->get();
    }

    public function compareQuery($segment)
    {
        return $this->baseCompareQuery()
            ->addSelect('dehydration')
            ->joinIntakeExam()
            ->whereNotNull('dehydration')
            ->withSegment($segment)
            ->get();
    }
}
