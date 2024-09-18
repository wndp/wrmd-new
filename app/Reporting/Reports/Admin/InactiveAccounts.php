<?php

namespace App\Reporting\Reports\Admin;

use App\Accounts\Account;
use App\Reporting\Contracts\ExportableReport;
use Illuminate\Database\Query\Builder;

class InactiveAccounts extends ExportableReport
{
    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.spreadsheet';
    }

    /**
     * Get the reports render options.
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
     * Get the records to be listed.
     */
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
            'ID',
            'Organization',
            'Contact name',
            'Country',
            'City',
            'Subdivision',
            'Phone Number',
            'Contact Email',
            'Date Created',
            'Patients Admitted',
        ];
    }

    /**
     * Prepare a query for an export.
     */
    public function query(): Builder
    {
        // No user has signed within a given time (6months, 1 year, ...)
        $query = Account::where('is_active', true)
            ->whereDate('last_signed_in_at', '<=', now()->subMonths(6))
            ->withCount('admissions');

        return $this->applyFilters($query);
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return [
            $row->id,
            $row->organization,
            $row->contact_name,
            $row->country,
            $row->city,
            $row->subdivision,
            $row->phone_number,
            $row->contact_email,
            $row->created_at,
            $row->admissions_count,
        ];
    }
}
