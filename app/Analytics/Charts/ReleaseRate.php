<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandlePieData;
use App\Analytics\Contracts\Chart;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class ReleaseRate extends Chart
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
        [$dispositionReleasedId, $notReleasedIds] = $this->dispositionIds();

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("sum(if(`disposition_id` = {$dispositionReleasedId}, 1, 0)) as `yes`")
            ->selectRaw("sum(if(`disposition_id` in ($notReleasedIds), 1, 0)) as `no`")
            ->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    public function compareQuery($segment)
    {
        [$dispositionReleasedId, $notReleasedIds] = $this->dispositionIds();

        $query = Admission::where('team_id', $this->team->id)
            ->selectRaw("sum(if(`disposition_id` = {$dispositionReleasedId}, 1, 0)) as `yes`")
            ->selectRaw("sum(if(`disposition_id` in ($notReleasedIds), 1, 0)) as `no`")
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $this->aggregateData($query->get());
    }

    private function dispositionIds()
    {
        [
            $dispositionReleasedId,
            $dispositionPendingId,
            $dispositionDeadIds,
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DEAD->value],
        ]);

        $notReleasedIds = implode(',', array_merge([$dispositionPendingId], $dispositionDeadIds));

        return [$dispositionReleasedId, $notReleasedIds];
    }

    public function aggregateData($data)
    {
        return $data->flatMap(function ($data) {
            return [
                ['subgroup' => 'Released', 'aggregate' => intval($data['yes'])],
                ['subgroup' => 'Not Released', 'aggregate' => intval($data['no'])],
            ];
        });
    }
}
