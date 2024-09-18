<?php

namespace App\Reporting\Reports;

use App\Hotline\Models\Incident;
use App\Reporting\Contracts\ExportableReport;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class HotlineIncidents extends ExportableReport
{
    /**
     * Get the report format options.
     */
    public function options(): array
    {
        return [
            'viewportSize' => 'auto',
            'viewportMargin' => '0 20px',
            'orientation' => 'landscape',
            'user-style-sheet' => public_path('css/report-landscape.css'),
        ];
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'hotline::reports.incidents';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Hotline Incidents Export');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return collect((new DateRange('reported_at'))->toArray());
    }

    public function data(): array
    {
        return [
            'headings' => $this->headings(),
            'data' => $this->query()->get()->map(function ($row) {
                return $this->map($row);
            }),
        ];
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return [
            __('Incident Number'),
            __('Date Reported'),
            __('Date Occurred'),
            __('Recorded By'),
            __('Duration of Call'),
            __('Category'),
            __('Priority'),
            __('Suspected Species'),
            __('Number of Animals'),
            __('Status'),
            __('Address / Location'),
            __('City'),
            __('State'),
            __('Postal Code'),
            __('Description'),
            __('Date Resolved'),
            __('Resolution'),
            __('Given Information'),
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        return Incident::where('account_id', $this->team->id)
            ->dateRange(
                Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format('Y-m-d'),
                Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format('Y-m-d'),
                'reported_at'
            );
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return [
            $row->incident_number,
            $row->reported_at,
            $row->occurred_at,
            $row->recorded_by,
            $row->duration_of_call,
            $row->category,
            $row->is_priority ? __('Yes') : __('No'),
            $row->suspected_species,
            $row->number_of_animals,
            $row->status,
            $row->location,
            $row->city,
            $row->subdivision,
            $row->postal_code,
            $row->description,
            $row->resolved_at,
            $row->resolution,
            $row->given_information ? __('Yes') : __('No'),
        ];
    }
}
