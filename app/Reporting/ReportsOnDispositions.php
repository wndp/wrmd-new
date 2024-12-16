<?php

namespace App\Reporting;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Admission;
use App\Models\AttributeOption;
use App\Reporting\Filters\SpeciesGrouping;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

trait ReportsOnDispositions
{
    /**
     * Get acquisition totals per species for taxonomic class.
     */
    protected function scopeAcquisitionTotals($class = null): Builder
    {
        $query = Admission::where('team_id', $this->team->id);

        if (! is_null($class)) {
            $query->whereIn('class', Arr::wrap($class));
        }

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

        $groupBy = $this->getAppliedFilterValue(SpeciesGrouping::class);

        $query
            ->when($groupBy === 'common_name', fn ($q) => $q->addSelect(DB::raw('ANY_VALUE(common_name) as common_name')))
            ->when($groupBy === 'taxon_id', fn ($q) => $q->addSelect(DB::raw('ANY_VALUE(taxon_id) as taxon_id')))
            ->addSelect(DB::raw('ANY_VALUE(class) as class'))
            //->select('common_name', 'taxon_id', 'class')
            ->addSelect(DB::raw('count(*) as `total`'))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $pendingPatientId, 1, 0)) as `pending`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $releasedPatientId, 1, 0)) as `released`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $transferredPatientId, 1, 0)) as `transferred`"))
            ->addSelect(DB::raw("sum(if(`disposition_id` = $doaPatientId, 1, 0)) as `doa`"))
            //->addSelect(DB::raw("sum(if(`disposition_id` = 'Died +24hr', 1, 0)) as `died_after_24`"))
            //->addSelect(DB::raw("sum(if(`disposition_id` = 'Died in 24hr', 1, 0)) as `died_in_24`"))
            ->addSelect(DB::raw('sum(if(`disposition_id` in ('.implode(',', $diedPatientId).'), 1, 0)) as `died`'))
            //->addSelect(DB::raw("sum(if(`disposition_id` = 'Euthanized +24hr', 1, 0)) as `euthanized_after_24`"))
            //->addSelect(DB::raw("sum(if(`disposition_id` = 'Euthanized in 24hr', 1, 0)) as `euthanized_in_24`"))
            ->addSelect(DB::raw('sum(if(`disposition_id` in ('.implode(',', $euthanizedPatientId).'), 1, 0)) as `euthanized`'))

            ->joinPatients()
            ->joinTaxa();

        $transferTypes = AttributeOption::getDropdownOptions([
            AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
        ])->first();

        foreach ($transferTypes as $id => $type) {
            $as = 'transfer_'.Str::slug($type, '_');
            $query->addSelect(DB::raw("sum(if(`transfer_type_id` = '$id', 1, 0)) as `$as`"));
        }

        return $query->groupBy($groupBy);
    }

    /**
     * Get release type totals per species for taxonomic class.
     */
    protected function scopeReleaseTypeTotals(string|array|null $class = null): Builder
    {
        $query = Admission::where('team_id', $this->team->id);

        if (! is_null($class)) {
            $query->whereIn('class', Arr::wrap($class));
        }

        $groupBy = $this->getAppliedFilterValue(SpeciesGrouping::class);

        $query
            ->when($groupBy === 'common_name', fn ($q) => $q->addSelect(DB::raw('ANY_VALUE(common_name) as common_name')))
            ->when($groupBy === 'taxon_id', fn ($q) => $q->addSelect(DB::raw('ANY_VALUE(taxon_id) as taxon_id')))
            ->addSelect(DB::raw('count(*) as `total`'))
            ->addSelect(DB::raw('sum(if(`release_type_id` is null, 1, 0)) as `unknown`'));

        $releaseTypes = AttributeOption::getDropdownOptions([
            AttributeOptionName::PATIENT_RELEASE_TYPES->value,
        ])->first();

        foreach ($releaseTypes as $id => $type) {
            $as = 'transfer_'.Str::slug($type, '_');
            $query->addSelect(DB::raw("sum(if(`release_type_id` = '$id', 1, 0)) as `$as`"));
        }

        [$releasedPatientId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_RELEASED->value,
        ]);

        return $query->joinPatients()
            ->joinTaxa()
            ->where('disposition_id', $releasedPatientId)
            ->groupBy($groupBy);
    }

    protected function scopeTransferTypeTotals($class = null)
    {
        $groupBy = $this->getAppliedFilterValue(SpeciesGrouping::class);

        $query = Admission::where('team_id', $this->team->id)
            ->when($groupBy === 'common_name', fn ($q) => $q->addSelect(DB::raw('ANY_VALUE(common_name) as common_name')))
            ->when($groupBy === 'taxon_id', fn ($q) => $q->addSelect(DB::raw('ANY_VALUE(taxon_id) as taxon_id')))
            ->addSelect(DB::raw('count(*) as `total`'))
            ->addSelect(DB::raw('sum(if(`transfer_type_id` is null, 1, 0)) as `unknown`'));

        $transferTypes = AttributeOption::getDropdownOptions([
            AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
        ])->first();

        foreach ($transferTypes as $id => $type) {
            $as = 'transfer_'.Str::slug($type, '_');
            $query->addSelect(DB::raw("sum(if(`transfer_type_id` = '$id', 1, 0)) as `$as`"));
        }

        if ($class) {
            $query->whereIn('class', Arr::wrap($class));
        }

        [$transferedPatientId] = \App\Models\AttributeOptionUiBehavior::getAttributeOptionUiBehaviorIds([
            AttributeOptionName::PATIENT_DISPOSITIONS->value,
            AttributeOptionUiBehavior::PATIENT_DISPOSITION_IS_TRANSFERRED->value,
        ]);

        return $this->applyFilters($query)->joinPatients()
            ->joinTaxa()
            ->where('disposition_id', $transferedPatientId)
            ->groupBy($groupBy)
            ->get();
    }
}
