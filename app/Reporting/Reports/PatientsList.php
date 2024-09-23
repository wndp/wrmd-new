<?php

namespace App\Reporting\Reports;

use App\Listing\Lists\SelectedPatientsList;
use App\Reporting\Contracts\ExportableReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PatientsList extends ExportableReport implements FromCollection
{
    protected $listHeaders;

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return __('List of Patients');
    }

    /**
     * Get the view path to render.
     */
    public function viewPath(): string
    {
        return 'reports.list';
    }

    /**
     * Get the records to be listed.
     */
    public function data(): array
    {
        $list = (new SelectedPatientsList($this->team))->get();

        return compact('list');
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        $this->listHeaders = Collection::make(
            (new SelectedPatientsList($this->team))->formatListHeaders()
        );

        return $this->listHeaders
            ->pluck('label')
            ->push(__('Date Admitted'))
            ->prepend(__('Common Name'))
            ->prepend(__('Case Number'))
            ->toArray();
    }

    public function collection(): Collection
    {
        return (new SelectedPatientsList($this->team))->get()['rows']['data'];
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return $this->listHeaders
            ->pluck('key')
            ->transform(fn ($key) => $row[$key])
            ->push($row['admitted_at'])
            ->prepend($row['common_name'])
            ->prepend($row['case_number'])
            ->toArray();
    }
}
