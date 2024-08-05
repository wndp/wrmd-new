<?php

namespace App\Analytics\Tables;

use App\Analytics\Contracts\Table;
use App\Analytics\MapIntoDataTableRow;

class CitiesFound extends Table
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        foreach ($this->filters->segments as $segment) {
            $this->series = $this->series->merge(
                $this->query($segment)
                    ->groupBy(function ($admission) {
                        return trim("$admission->city_found $admission->subdivision_found");
                    })
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
                        ->groupBy(function ($admission) {
                            return trim("$admission->city_found $admission->subdivision_found");
                        })
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
            ->addSelect('city_found', 'subdivision_found')
            ->withSegment($segment)
            ->get();
    }

    public function compareQuery($segment)
    {
        return $this->baseCompareQuery()
            ->addSelect('city_found', 'subdivision_found')
            ->withSegment($segment)
            ->get();
    }
}
