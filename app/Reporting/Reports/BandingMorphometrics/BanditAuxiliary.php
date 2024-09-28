<?php

namespace App\Reporting\Reports\BandingMorphometrics;

use App\Models\Admission;
use App\Reporting\Contracts\ExportableReport;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BanditAuxiliary extends ExportableReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.research.bandit-auxiliary';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Bandit Auxiliary Markers');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect((new DateRange('banded_at'))->toArray());
    }

    /**
     * Get the data for the annual report.
     */
    public function data(): array
    {
        $headings = $this->headings();
        $bandings = $this->query()->get()->map(function ($row) {
            return $this->map($row);
        });
        $dateFrom = Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format(config('wrmd.date_format'));
        $dateTo = Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format(config('wrmd.date_format'));

        return compact('headings', 'bandings', 'dateFrom', 'dateTo');
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return [
            'WRMD Case Number',
            'Band Number',
            'Marker Type 1',
            'Marker Code 1',
            'Marker Color 1',
            'Marker Code Color 1',
            'Side of Bird 1',
            'Placed on Leg 1',
        ];
    }

    /**
     * Prepare a query for an export.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function query()
    {
        $query = Admission::where('team_id', $this->team->id)
            ->select([
                'patients.common_name',
                'bandings.*',
                'morphometrics.*',
            ])
            ->addSelect([
                'admissions.case_id as id',
                'admissions.case_year as case_year',
                'admissions.team_id as team_id',
                'admissions.patient_id as patient_id',
                'bandings.remarks as remarks',
            ])
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->join('bandings', 'patients.id', '=', 'bandings.patient_id')
            ->leftJoin('morphometrics', 'patients.id', '=', 'morphometrics.patient_id')
            ->whereNotNull('auxiliary_marker');

        return $this->applyFilters($query);
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return [
            $row->caseNumber,
            $row->band_number,
            $row->auxiliary_marker_type,
            $row->auxiliary_marker,
            $row->auxiliary_marker_color,
            $row->auxiliary_marker_code_color,
            $row->auxiliary_side_of_bird,
            $row->auxiliary_placement_on_leg,
        ];
    }
}
