<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class TransferRate extends Chart
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
        [$dispositionTransferredId, $notTransferredIds] = $this->dispositionIds();

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("sum(if(`disposition_id` = {$dispositionTransferredId}, 1, 0)) as `yes`")
            ->selectRaw("sum(if(`disposition_id` in ($notTransferredIds), 1, 0)) as `no`")
            ->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    public function compareQuery($segment)
    {
        [$dispositionTransferredId, $notTransferredIds] = $this->dispositionIds();

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("sum(if(`disposition_id` = {$dispositionTransferredId}, 1, 0)) as `yes`")
            ->selectRaw("sum(if(`disposition_id` in ($notTransferredIds), 1, 0)) as `no`")
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    private function dispositionIds()
    {
        [
            $dispositionTransferredId,
            $dispositionPendingId,
            $dispositionDeadIds,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DEAD->value],
        ]);

        $notReleasedIds = implode(',', array_merge([$dispositionPendingId], $dispositionDeadIds));

        return [$dispositionTransferredId, $notReleasedIds];
    }

    public function aggregateData($data)
    {
        return $data->flatMap(function ($data) {
            return [
                ['subgroup' => 'Transferred', 'aggregate' => intval($data['yes'])],
                ['subgroup' => 'Not Transferred', 'aggregate' => intval($data['no'])],
            ];
        });
    }
}
