<?php

namespace App\Reporting\Reports;

use App\Reporting\Contracts\Report;

class AssetReport extends Report
{
    protected $path;

    protected $title;

    public function __construct($path, $title)
    {
        $this->path = asset($path);
        $this->title = $title;
    }

    /**
     * Get the title of the report.
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * Get the path to the reports view file.
     */
    public function viewPath(): string
    {
        return $this->path;
    }

    /**
     * Get the reports data.
     */
    protected function data(): array
    {
        return [];
    }

    /**
     * Determine if the report can be favorited.
     */
    public function canFavorite(): bool
    {
        return false;
    }

    /**
     * Determine if the report can be exported.
     */
    public function canExport(): bool
    {
        return false;
    }
}
