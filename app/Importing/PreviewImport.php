<?php

namespace App\Importing;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;

class PreviewImport implements WithCustomValueBinder, WithHeadingRow, WithLimit
{
    use Importable;
    use ImportValueBinder;

    protected $limit;

    protected $startRow;

    public function __construct($limit = 5, $startRow = 1)
    {
        $this->limit = $limit;
        $this->startRow = $startRow;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function startRow(): int
    {
        return $this->startRow;
    }
}
