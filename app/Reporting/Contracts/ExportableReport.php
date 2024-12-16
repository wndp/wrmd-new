<?php

namespace App\Reporting\Contracts;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

abstract class ExportableReport extends Report implements FromQuery, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithProperties, WithTitle
{
    public $canExport = true;

    public function registerEvents(): array
    {
        return [
            // Bold the header row
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A1:Z1')->getFont()->setBold(true);
            },
        ];
    }

    /**
     * Excel worksheet properties.
     */
    public function properties(): array
    {
        return [];
    }

    /**
     * The export column headers.
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        return new Collection;
    }

    /**
     * @param  mixed  $row
     */
    public function map($row): array
    {
        return $row->toArray();
    }
}
