<?php

namespace App\Reporting\Generators;

use App\Reporting\Contracts\ExportableReport;
use App\Reporting\Contracts\Generator;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class ExcelX extends Generator
{
    /**
     * Generate the report.
     */
    public function handle(): void
    {
        abort_unless($this->report instanceof ExportableReport, 404);

        $this->filePath = $this->dirname().$this->basename().'.xlsx';

        ExcelFacade::store($this->report, $this->filePath, 's3', Excel::XLSX);
    }
}
