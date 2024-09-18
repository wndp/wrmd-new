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

class FrugalRescuers extends ExportableReport
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
        return __('Rescuers Who Have Not Donated');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge((new DateRange('admitted_at'))->toArray());
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return __("This report lists all rescuers in your account who's patients were admitted within the date range specified and have not made a donation.");
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
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        return Person::where('team_id', $this->team->id)
            ->whereHas('patients', function ($query) {
                $query->dateRange(
                    Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format('Y-m-d'),
                    Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format('Y-m-d'),
                    'date_admitted_at'
                );
            })
            ->whereDoesntHave('donations');
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
        ];
    }
}
