<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class ReleasePercentages extends Chart
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
        [$dispositionReleasedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value,
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, release_type_id as subgroup')
            ->joinPatients()
            ->whereNotNull('release_type_id')
            ->where('disposition_id', $dispositionReleasedId)
            ->orderByDesc('aggregate')
            ->groupBy('release_type_id')
            ->limit(10);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        [$dispositionReleasedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value,
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, release_type_id as subgroup')
            ->joinPatients()
            ->whereNotNull('release_type_id')
            ->where('disposition_id', $dispositionReleasedId)
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->orderByDesc('aggregate')
            ->groupBy('release_type_id')
            ->limit(10);

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
