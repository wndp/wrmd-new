<?php

namespace App\Reporting\Contracts;

abstract class ExportableSheet extends ExportableReport
{
    /**
     * Get the path to the reports view file.
     */
    public function viewPath(): string
    {
        return '';
    }

    /**
     * Get the reports data.
     */
    protected function data(): array
    {
        return [];
    }
}
