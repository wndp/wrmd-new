<?php

namespace App\Reporting\Reports\Disposition;

use App\Reporting\Contracts\ExportableSheet;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Collection;
use Illuminate\Support\Fluent;
use Illuminate\Support\Number;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DispositionStatsPerTaxonomySheet extends ExportableSheet implements FromCollection, WithStrictNullComparison
{
    use ReportsOnDispositions;

    public function title(): string
    {
        return $this->request->class;
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return [
            'Common Name',
            'Total',
            'R',
            'T',
            'P',
            'E',
            'E +24hr',
            'D',
            'D +24hr',
            'DOA',
            'Survival Rate Including %',
            'Survival Rate After %',
        ];
    }

    public function collection(): Collection
    {
        return $this->applyFilters($this->scopeAcquisitionTotals($this->request->class))
            ->get()
            ->map(function ($dispositions) {
                return new Fluent([
                    'common_name' => $dispositions->common_name,
                    'total' => $dispositions->total,
                    'released' => $dispositions->released,
                    'transferred' => $dispositions->transferred,
                    'pending' => $dispositions->pending,
                    'euthanized_in_24' => $dispositions->euthanized_in_24,
                    'euthanized_after_24' => $dispositions->euthanized_after_24,
                    'died_in_24' => $dispositions->died_in_24,
                    'died_after_24' => $dispositions->died_after_24,
                    'doa' => $dispositions->doa,
                    'survival_rate' => Number::survivalRate($dispositions),
                    'survival_rate_after' => Number::survivalRate($dispositions, true),
                ]);
            });
    }
}
