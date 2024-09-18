<?php

namespace App\Analytics\Charts;

use App\Analytics\Concerns\HandleSeriesNames;
use App\Analytics\Contracts\Chart;
use App\Analytics\DataSet;
use App\Analytics\SurvivalRateCollection;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use Illuminate\Support\Facades\DB;

class SurvivalRate extends Chart
{
    use HandleSeriesNames;

    /**
     * {@inheritdoc}
     */
    public function compute()
    {
        $this->series->localMerge([
            [
                'name' => 'Including first 24 hours',
                'data' => [],
            ],
        ])
            ->localMerge([
                [
                    'name' => 'After first 24 hours',
                    'data' => [],
                ],
            ]);

        foreach ($this->filters->segments as $segment) {
            $this->categories->push(
                $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->date_from, $this->filters->date_to)
            );

            $this->query($segment)
                ->calculateSurvivalRates()
                ->each(function ($dataset) {
                    $this->series
                        ->pushOnToSeriesData('Including first 24 hours', $dataset->get('including24Hours'))
                        ->pushOnToSeriesData('After first 24 hours', $dataset->get('after24Hours'));
                });

            if ($this->filters->compare) {
                $this->categories->push(
                    $this->formatSeriesName($segment, $segment, $this->filters->compare, $this->filters->compare_date_from, $this->filters->compare_date_to)
                );

                $this->compareQuery($segment)
                    ->calculateSurvivalRates()
                    ->each(function ($dataset) {
                        $this->series
                            ->pushOnToSeriesData('Including first 24 hours', $dataset->get('including24Hours'))
                            ->pushOnToSeriesData('After first 24 hours', $dataset->get('after24Hours'));
                    });
            }
        }
    }

    public function query($segment)
    {
        [
            $pendingPatientId,
            $releasedPatientId,
            $transferredPatientId,
            $doaPatientId,
            $diedPatientId,
            $euthanizedPatientId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DOA->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DIED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_EUTHANIZED->value]
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->addSelect(DB::raw('count(*) as `total`'))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $pendingPatientId, 1, 0)) as `pending`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $releasedPatientId, 1, 0)) as `released`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $transferredPatientId, 1, 0)) as `transferred`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $doaPatientId, 1, 0)) as `doa`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` in (".implode(',', $diedPatientId)."), 1, 0)) as `died`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` in (".implode(',', $euthanizedPatientId)."), 1, 0)) as `euthanized`"))
            ->joinPatients();

        if ($this->filters->date_period !== 'all-dates') {
            $query->dateRange($this->filters->date_from, $this->filters->date_to, 'date_admitted_at');
        }

        $this->withSegment($query, $segment);

        return new SurvivalRateCollection($query->get()->mapInto(DataSet::class));
    }

    public function compareQuery($segment)
    {
        [
            $pendingPatientId,
            $releasedPatientId,
            $transferredPatientId,
            $doaPatientId,
            $diedPatientId,
            $euthanizedPatientId
        ] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_PENDING->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DOA->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_DIED->value],
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_EUTHANIZED->value]
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->addSelect(DB::raw('count(*) as `total`'))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $pendingPatientId, 1, 0)) as `pending`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $releasedPatientId, 1, 0)) as `released`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $transferredPatientId, 1, 0)) as `transferred`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $doaPatientId, 1, 0)) as `doa`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` in (".implode(',', $diedPatientId)."), 1, 0)) as `died`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` in (".implode(',', $euthanizedPatientId)."), 1, 0)) as `euthanized`"))
            ->joinPatients()
            ->dateRange($this->filters->compare_date_from, $this->filters->compare_date_to, 'date_admitted_at');

        $this->withSegment($query, $segment);

        return new SurvivalRateCollection($query->get()->mapInto(DataSet::class));
    }
}
