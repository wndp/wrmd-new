<?php

namespace App\Reporting\Reports\Disposition;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\IncludedTaxonomies;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DispositionsByDate extends Report
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.disposition.dispositions-by-date';
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge((new DateRange('dispositioned_at'))->toArray());
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class, (new IncludedTaxonomies)->default());

        return [
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
            'data' => $this->getData(),
        ];
    }

    /**
     * ?
     *
     * @return Query
     */
    protected function getData()
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
            [AttributeOptionName::PATIENT_DISPOSITIONS->value, AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_EUTHANIZED->value],
        ]);

        $query = Admission::where('team_id', $this->team->id)
            ->select('dispositioned_at')
            ->addSelect(DB::raw('count(*) as `total`'))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $pendingPatientId, 1, 0)) as `pending`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $releasedPatientId, 1, 0)) as `released`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $transferredPatientId, 1, 0)) as `transferred`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $doaPatientId, 1, 0)) as `doa`"))
            ->addSelect(DB::raw('sum(if(`disposition_id` in ('.implode(',', $diedPatientId).'), 1, 0)) as `died`'))
            ->addSelect(DB::raw('sum(if(`disposition_id` in ('.implode(',', $euthanizedPatientId).'), 1, 0)) as `euthanized`'));

        return $this->applyFilters($query)
            ->joinPatients()
            ->joinTaxa()
            ->orderBy('dispositioned_at')
            ->groupBy('dispositioned_at')
            ->get();
    }
}
