<?php

namespace App\Analytics\Tables;

use App\Analytics\Contracts\Table;
use App\Analytics\MapIntoDataTableRow;
use Illuminate\Support\Collection;

class PatientsByBiologicalGroups extends Table
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $groups = trans('terminology.lay_groups.Group');

        foreach ($this->filters->segments as $segment) {
            $this->series = $this->series->merge(
                $this->query($segment)
                    ->map(function ($admission) use ($groups) {
                        $admission->group = (new Collection(json_decode($admission->lay_groups)))->intersect($groups)->first();

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
                        ->map(function ($admission) use ($groups) {
                            $admission->group = (new Collection(json_decode($admission->lay_groups)))->intersect($groups)->first();

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
            ->addSelect('lay_groups')
            ->joinTaxa()
            ->withSegment($segment)
            ->get();
    }

    public function compareQuery($segment)
    {
        return $this->baseCompareQuery()
            ->addSelect('lay_groups')
            ->joinTaxa()
            ->withSegment($segment)
            ->get();
    }
}
