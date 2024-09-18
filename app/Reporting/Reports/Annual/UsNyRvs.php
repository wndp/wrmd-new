<?php

namespace App\Reporting\Reports\Annual;

use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Contracts\Generator;
use App\Reporting\Generators\Mpdf;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Collection;

/**
 * New York, United Sates.
 */
class UsNyRvs extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the pdf format options.
     */
    public function options(): array
    {
        return [
            //'format' => 'letter-L',
            'template' => public_path('pdfs/new_york_rabies_vector_species.pdf'),
            'templateContent' => [
                1 => 'reporting.reports.annual.usNyRvs',
                //2 => 'reporting.reports.annual.usNyTally2',
            ],
        ];
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reporting.reports.no-preview';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'New York Rabies Vector Species Log and Tally';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        return [
            'year' => $this->year,
            'speciesTotals' => $this->speciesTotals(),
        ];
    }

    public function sumDispositions($collection, $group)
    {
        return [
            'released' => $collection->get($group)?->sum('released') ?: 0,
            'pending' => $collection->get($group)?->sum('pending') ?: 0,
            'transferred' => $collection->get($group)?->sum('transfer_continued_care') ?: 0,
            'transferred_non_releasable' => $collection->get($group)?->sum('transfer_education_or_scientific_research_individual') + $collection->get($group)?->sum('transfer_education_or_scientific_research_institute') ?: 0,
            'doa' => $collection->get($group)?->sum('doa') ?: 0,
            'died' => $collection->get($group)?->sum('died') ?: 0,
            'euthanized' => $collection->get($group)?->sum('euthanized') ?: 0,
        ];
    }

    /**
     * Get the pdf generator use by the report.
     */
    protected function pdfGenerator(): Generator
    {
        return new Mpdf($this);
    }

    /**
     * Get the totals for the NY distress codes.
     */
    private function speciesTotals(): Collection
    {
        return $this->scopeAcquisitionTotals('Mammalia')
            ->addSelect('order', 'family', 'genus')
            ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
            ->get()
            ->groupBy(function ($species) {
                if ($species->order === 'Chiroptera') {
                    return 'bats';
                } elseif ($species->family === 'Mephitidae') {
                    return 'skunks';
                } elseif ($species->genus === 'Procyon') {
                    return 'raccoons';
                }

                return 'other';
            })
            ->pipe(function ($collection) {
                $bats = $this->sumDispositions($collection, 'bats');
                $skunks = $this->sumDispositions($collection, 'skunks');
                $raccoons = $this->sumDispositions($collection, 'raccoons');
                $other = $this->sumDispositions($collection, 'other');

                return collect([
                    'bats' => array_merge($bats, [
                        'total' => array_sum($bats),
                    ]),
                    'skunks' => array_merge($skunks, [
                        'total' => array_sum($skunks),
                    ]),
                    'raccoons' => array_merge($raccoons, [
                        'total' => array_sum($raccoons),
                    ]),
                    'other' => array_merge($other, [
                        'total' => array_sum($other),
                    ]),
                ]);
            });
    }
}
