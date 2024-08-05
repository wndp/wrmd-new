<?php

namespace App\Analytics\Charts;

use App\Models\Admission;
use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;

class PatientsByMucousMembrane extends Chart
{
    use HandlePieData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handlePieData();
    }

    public function query($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("count(*) as aggregate, concat(mm_color, ' and ', mm_texture) as subgroup")
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('mm_color')
            ->orderByDesc('aggregate')
            ->groupBy('subgroup');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to);
        }

        $this->withSegment($query, $segment);

        return $query->get()->filter(function ($data) {
            return ! is_null($data->subgroup);
        });
    }

    public function compareQuery($segment)
    {
        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("count(*) as aggregate, concat(mm_color, ' and ', mm_texture) as subgroup")
            ->joinPatients()
            ->joinIntakeExam()
            ->whereNotNull('mm_color')
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to)
            ->orderByDesc('aggregate')
            ->groupBy('subgroup');

        $this->withSegment($query, $segment);

        return $query->get()->filter(function ($data) {
            return ! is_null($data->subgroup);
        });
    }
}
