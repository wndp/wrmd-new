<?php

namespace App\Analytics\Tables;

use App\Analytics\Contracts\Table;
use App\Analytics\MapIntoDataTableRow;
use App\Analytics\Series;
use Illuminate\Support\Facades\DB;

class PatientsByAdmissionMucousMembranes extends Table
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
                    ->groupBy('mucous_membrane')
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
                        ->groupBy('mucous_membrane')
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
            ->addSelect(DB::raw('concat(mucous_membrane_color_id, " and ", mucous_membrane_texture_id) as mucous_membrane'))
            ->joinIntakeExam()
            ->whereNotNull('mucous_membrane_color_id')
            ->withSegment($segment)
            ->get()
            ->filter(function ($data) {
                return ! is_null($data->mucous_membrane);
            });
    }

    public function compareQuery($segment)
    {
        return $this->baseCompareQuery()
            ->addSelect(DB::raw('concat(mucous_membrane_color_id, " and ", mucous_membrane_texture_id) as mucous_membrane'))
            ->joinIntakeExam()
            ->whereNotNull('mucous_membrane_color_id')
            ->withSegment($segment)
            ->get()
            ->filter(function ($data) {
                return ! is_null($data->mucous_membrane);
            });
    }
}
