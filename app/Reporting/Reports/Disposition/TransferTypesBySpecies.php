<?php

namespace App\Reporting\Reports\Disposition;

use App\Enums\AttributeOptionName;
use App\Models\Admission;
use App\Models\AttributeOption;
use App\Patients\PatientOptions;
use App\Reporting\Contracts\Report;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use App\Reporting\Filters\IncludedTaxonomies;
use App\Reporting\Filters\SpeciesGrouping;
use App\Reporting\ReportsOnDispositions;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferTypesBySpecies extends Report
{
    use ReportsOnDispositions;

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.disposition.transfer-types-by-species';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Transfer Types by Species');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect(array_merge((new DateRange('dispositioned_at'))->toArray(), [
            new IncludedTaxonomies(),
            new SpeciesGrouping(),
        ]));
    }

    /**
     * Get total homecare hours by caregiver.
     */
    public function data(): array
    {
        $includedTaxonomies = $this->getAppliedFilterValue(IncludedTaxonomies::class, (new IncludedTaxonomies())->default());

        $transferTypes = AttributeOption::getDropdownOptions([
            AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
        ])->first();

        return [
            'dateFrom' => Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format')),
            'dateTo' => Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format')),
            'map' => [
                'amphibian' => in_array('Amphibia', $includedTaxonomies) ? $this->scopeTransferTypeTotals('Amphibia') : [],
                'bird' => in_array('Aves', $includedTaxonomies) ? $this->scopeTransferTypeTotals('Aves') : [],
                'mammal' => in_array('Mammalia', $includedTaxonomies) ? $this->scopeTransferTypeTotals('Mammalia') : [],
                'reptile' => in_array('Reptilia', $includedTaxonomies) ? $this->scopeTransferTypeTotals('Reptilia') : [],
                'unidentified' => in_array('Unidentified', $includedTaxonomies) ? $this->scopeTransferTypeTotals('Unidentified') : [],
            ],
            'grand' => $this->scopeTransferTypeTotals($includedTaxonomies),
            'transferTypes' => $transferTypes
        ];
    }

    /**
     * Get the pdf generator use by the report.
     */
    protected function pdfGenerator(): \App\Reporting\Contracts\Generator
    {
        return new \App\Reporting\Generators\HeadlessChrome($this);
    }
}
