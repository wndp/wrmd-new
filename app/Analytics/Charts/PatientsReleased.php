<?php

namespace App\Analytics\Charts;

use App\Analytics\ChronologicalCollection;
use App\Analytics\Concerns\HandleChronologicalData;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class PatientsReleased extends Chart
{
    use HandleChronologicalData;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->handleChronologicalData();
    }

    /**
     * Query for the requested data.
     */
    public function query($segment): ChronologicalCollection
    {
        [$dispositionReleasedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value,
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->selectRaw('count(*) as aggregate, date(dispositioned_at) as date')
            ->where('disposition_id', $dispositionReleasedId)
            ->groupBy('date')
            ->orderBy('date');

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'dispositioned_at');
        }

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }

    /**
     * Query for the requested comparative data.
     */
    public function compareQuery($segment): ChronologicalCollection
    {
        [$dispositionReleasedId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value,
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, date(dispositioned_at) as date')
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'dispositioned_at')
            ->where('disposition_id', $dispositionReleasedId)
            ->groupBy('date')
            ->orderBy('date');

        $this->withSegment($query, $segment);

        return new ChronologicalCollection($query->get()->mapInto(DataSet::class));
    }
}
