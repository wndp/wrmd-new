<?php

namespace App\Analytics\Numbers;

use App\Analytics\Contracts\Number;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;

class PatientsEuthanized extends Number
{
    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $segment = $this->filters->segments[0];

        $now = $this->query($segment);
        $compare = $this->filters->compare ? $this->compareQuery($segment) : null;

        $this->calculatePercentageDifference($now, $compare);

        $this->now = number_format($now);
        $this->prev = is_null($compare) ? null : number_format($compare);
    }

    public function query($segment)
    {
        [$dispositionEuthanizedIds] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_EUTHANIZED->value,
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->whereIn('disposition_id', $dispositionEuthanizedIds);

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return $query->count();
    }

    public function compareQuery($segment)
    {
        [$dispositionEuthanizedIds] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_EUTHANIZED->value,
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->joinPatients()
            ->whereIn('disposition_id', $dispositionEuthanizedIds)
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return $query->count();
    }
}
