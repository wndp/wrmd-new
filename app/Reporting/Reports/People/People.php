<?php

namespace App\Reporting\Reports\People;

use App\Models\Person;
use App\Reporting\Contracts\ExportableReport;
use App\Reporting\Filters\DateFrom;
use App\Reporting\Filters\DateRange;
use App\Reporting\Filters\DateTo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class People extends ExportableReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.spreadsheet';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('All People');
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return __('This report lists all people created in your account within the date range specified; regardless of their affiliation to you.');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge((new DateRange('created_at'))->toArray());
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
            'Id',
            __('Organization'),
            __('First Name'),
            __('Last Name'),
            __('Phone Number'),
            __('Alt. Phone Number'),
            __('Email'),
            __('Country'),
            __('Address'),
            __('City'),
            __('Subdivision'),
            __('Postal Code'),
            __('Latitude'),
            __('Longitude'),
            __('County'),
            __('Notes'),
            __('No Solicitations?'),
            __('Is a Volunteer?'),
            __('Is a Member?'),
            __('Is Rescuer'),
            __('Date Created'),
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        $dateFrom = Carbon::today($this->team->settingsStore()->get('timezone'))->isSameDay($this->getAppliedFilterValue(DateFrom::class))
            ? Carbon::now()
            : Carbon::parse($this->getAppliedFilterValue(DateFrom::class));

        $dateTo = Carbon::today($this->team->settingsStore()->get('timezone'))->isSameDay($this->getAppliedFilterValue(DateTo::class))
            ? Carbon::now()
            : Carbon::parse($this->getAppliedFilterValue(DateTo::class));

        return Person::where('team_id', $this->team->id)
            ->dateRange(
                $dateFrom->format('Y-m-d'),
                $dateTo->format('Y-m-d'),
                'created_at'
            )
            ->with('patients');
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->organization,
            $row->first_name,
            $row->last_name,
            $row->phone,
            $row->alt_phone,
            $row->email,
            $row->country,
            $row->address,
            $row->city,
            $row->subdivision,
            $row->postal_code,
            $row->lat,
            $row->lng,
            $row->county,
            $row->notes,
            $row->no_solicitations ? __('Yes') : __('No'),
            $row->is_volunteer ? __('Yes') : __('No'),
            $row->is_member ? __('Yes') : __('No'),
            $row->patients->isNotEmpty() ? __('Yes') : __('No'),
            $row->created_at->tz($this->team->settingsStore()->get('timezone'))->format('Y-m-d'),
        ];
    }
}
