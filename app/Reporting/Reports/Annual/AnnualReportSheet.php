<?php

namespace App\Reporting\Reports\Annual;

use App\Reporting\Contracts\ExportableSheet;
use Maatwebsite\Excel\Concerns\FromCollection;

class AnnualReportSheet extends ExportableSheet implements FromCollection
{
    public function title(): string
    {
        return $this->request->title;
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return $this->request->headings;
    }

    public function collection(): Collection
    {
        return $this->request->rows;
    }
}
