<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class TransferPercentages extends Chart
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
        [$dispositionTransferredId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, transfer_type_id as subgroup')
            ->joinPatients()
            ->whereNotNull('transfer_type_id')
            ->where('disposition_id', $dispositionTransferredId)
            ->groupBy('transfer_type_id')
            ->orderByDesc('aggregate')
            ->limit(10);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->get();
    }

    public function compareQuery($segment)
    {
        [$dispositionTransferredId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw('count(*) as aggregate, transfer_type_id as subgroup')
            ->joinPatients()
            ->whereNotNull('transfer_type_id')
            ->where('disposition_id', $dispositionTransferredId)
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at')
            ->groupBy('transfer_type_id')
            ->orderByDesc('aggregate')
            ->limit(10);

        $this->withSegment($query, $segment);

        return $query->get();
    }
}
