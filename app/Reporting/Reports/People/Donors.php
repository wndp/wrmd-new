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

class Donors extends ExportableReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.people.donors';
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('Donors');
    }

    /**
     * Get the reports explanation.
     */
    public function explanation(): string
    {
        return __('This report lists all donors in your account who have donated within the date range specified.');
    }

    /**
     * {@inheritdoc}
     */
    public function filters(): Collection
    {
        return parent::filters()->merge((new DateRange('donated_at'))->toArray());
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
            __('Donation Date'),
            __('Value'),
            __('Method'),
            __('Comments'),
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        return Person::where('team_id', $this->team->id)
            ->whereHas('donations', function ($query) {
                $query->dateRange(
                    Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format('Y-m-d'),
                    Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format('Y-m-d'),
                    'donated_at'
                );
            })
            ->with(['donations' => function ($query) {
                $query->dateRange(
                    Carbon::parse($this->getAppliedFilterValue(DateFrom::class))->format('Y-m-d'),
                    Carbon::parse($this->getAppliedFilterValue(DateTo::class))->format('Y-m-d'),
                    'donated_at'
                );
            }]);
    }

    /**
     * @param  mixed  $row
     */
    public function map($donor): array
    {
        $donations = [];

        foreach ($donor->donations as $donation) {
            $donations[] = [
                $donor->id,
                $donor->organization,
                $donor->first_name,
                $donor->last_name,
                $donor->phone,
                $donor->alt_phone,
                $donor->email,
                $donor->country,
                $donor->address,
                $donor->city,
                $donor->subdivision,
                $donor->postal_code,
                $donor->lat,
                $donor->lng,
                $donor->county,
                $donor->notes,
                $donor->no_solicitations ? __('Yes') : __('No'),
                $donor->is_volunteer ? __('Yes') : __('No'),
                $donor->is_member ? __('Yes') : __('No'),
                $donation->donated_at->format('Y-m-d'),
                $donation->value,
                $donation->method,
                $donation->comments,
            ];
        }

        return $donations;
    }
}
