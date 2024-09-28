<?php

namespace App\Reporting\Reports\BandingMorphometrics;

use App\Models\Admission;
use App\Reporting\Contracts\ExportableReport;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BanditRecapture extends ExportableReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.research.bandit-recapture';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Bandit Recaptures');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect((new DateRange('recaptured_at'))->toArray());
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
            'Band Size',
            'Disposition',
            'Species',
            'Recapture Date',
            'Recapture Date Year',
            'Recapture Date Month',
            'Recapture Date Day',
            'Age',
            'How Aged',
            'Sex',
            'How Sexed',
            'Bird Status',
            'Location',
            'Bander ID',
            'Banded By',
            'Present Condition',
            'How Obtained',
            'Remarks',
            'Bill Height',
            'Bill Length',
            'Bill Width',
            'Bird Weight',
            'Culmen Length',
            'Exposed Culmen Length',
            'Tail Length',
            'Tarsus Length',
            'Wing Chord',
            'Blood Sample Taken',
            'Ectoparasites Present',
            'Ectoparasites Collected',
            'Feather Sample Taken',
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
            ->whereNotNull('band_number')
            ->whereNotNull('recaptured_at');

        return $this->applyFilters($query);
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        $samples = Arr::wrap(json_decode($row->samples_collected, true));

        return [
            $row->caseNumber,
            $row->band_number,
            $row->band_size,
            $row->recapture_disposition,
            $row->common_name,
            $row->recaptured_at?->format('m/d/Y'),
            $row->recaptured_at?->format('Y'),
            $row->recaptured_at?->format('m'),
            $row->recaptured_at?->format('d'),
            $row->age_code,
            $row->how_aged,
            $row->sex_code,
            $row->how_sexed,
            $row->status_code.$row->additional_status_code,
            $row->location_id,
            $row->master_bander_id,
            $row->banded_by,
            $row->present_condition,
            $row->how_present_condition,
            $row->remarks,
            $row->bill_depth,
            $row->bill_length,
            $row->bill_width,
            $row->weight,
            $row->culmen,
            $row->exposed_culmen,
            $row->tail_length,
            $row->tarsus_length,
            $row->wing_chord,
            in_array('Blood', $samples) ? 'Yes' : 'No',
            in_array('Parasites', $samples) ? 'Yes' : 'No',
            in_array('Parasites', $samples) ? 'Yes' : 'No',
            in_array('Feathers', $samples) ? 'Yes' : 'No',
        ];
    }
}
