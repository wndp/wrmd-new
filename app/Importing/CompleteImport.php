<?php

namespace App\Importing;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CompleteImport implements WithCustomValueBinder, WithHeadingRow
{
    use Importable;
    use ImportValueBinder;
}
