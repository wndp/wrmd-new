<?php

namespace App\Reporting\Reports\Annual;

use App\Classifications\UsNyCauseOfDistress;
use App\Models\Admission;
use App\Reporting\Contracts\AnnualReport;
use App\Reporting\Contracts\Generator;
use App\Reporting\Generators\Mpdf;
use App\Reporting\ReportsOnDispositions;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * New York, United Sates
 */
class UsNyTally extends AnnualReport
{
    use ReportsOnDispositions;

    /**
     * Get the pdf generator use by the report.
     */
    protected function pdfGenerator(): Generator
    {
        return new Mpdf($this);
    }

    /**
     * Get the pdf format options.
     */
    public function options(): array
    {
        return [
            'format' => 'letter-L',
            'template' => public_path('pdfs/newyorktally.pdf'),
            'templateContent' => [
                1 => 'reports.annual.usNyTally1',
                2 => 'reports.annual.usNyTally2',
            ],
        ];
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.no-preview';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return 'New York Wildlife Rehabilitation Tally';
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        parent::data();

        $care = DB::raw("sum(if(`transfer_type` = 'Continued care', 1, 0)) as `care`");
        $individual = DB::raw("sum(if(`transfer_type` like '%individual%', 1, 0)) as `individual`");
        $institute = DB::raw("sum(if(`transfer_type` like '%institute%', 1, 0)) as `institute`");

        return [
            'year' => $this->year,

            'amphibianAcquisitions' => $this->scopeAcquisitionTotals('Amphibia')
                ->addSelect($care)
                ->addSelect($individual)
                ->addSelect($institute)
                ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
                ->get(),

            'birdAcquisitions' => $this->scopeAcquisitionTotals('Aves')
                ->addSelect($care)
                ->addSelect($individual)
                ->addSelect($institute)
                ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
                ->get(),

            'mammalAcquisitions' => $this->scopeAcquisitionTotals('Mammalia')
                ->addSelect($care)
                ->addSelect($individual)
                ->addSelect($institute)
                ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
                ->get(),

            'reptileAcquisitions' => $this->scopeAcquisitionTotals('Reptilia')
                ->addSelect($care)
                ->addSelect($individual)
                ->addSelect($institute)
                ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
                ->get(),

            'distressCodesTotals' => $this->distressCodesTotals(),
        ];
    }

    /**
     * Get the totals for the NY distress codes.
     */
    private function distressCodesTotals(): Collection
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select('class')
            ->joinPatients()
            ->joinTaxa()
            ->join('patient_model_predictions', function ($join) {
                $join->on('patients.id', '=', 'patient_model_predictions.patient_id')
                    ->where('category', 'UsNyCauseOfDistress');
            })
            ->where('disposition', '!=', 'Void')
            ->dateRange(($this->year - 1).'-12-01', $this->year.'-11-30')
            ->groupBy('class');

        foreach (UsNyCauseOfDistress::terms() as $code) {
            $code = Str::lower(Str::before($code, '.'));
            $query->selectRaw("sum(if(`prediction` like ?, 1, 0)) as '$code'", ["$code.%"]);
        }

        return $query->get();
    }
}
