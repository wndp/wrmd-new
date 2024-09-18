<?php

namespace App\Reporting;

use Illuminate\Support\Str;

class ReportGroup
{
    public $title;

    public $titleSlug;

    public $reports;

    public $priority = 10;

    public $visibility = true;

    public $patientReports = false;

    public function __construct($title, array ...$reports)
    {
        $this->title = $title;
        $this->titleSlug = Str::slug($title);
        $this->reports = $reports;
    }

    /**
     * Set the order priority of the report group.
     */
    public function setPriority(int $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set the visibility of the report group.
     */
    public function setVisibility(bool $visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Set the patientReports of the report group.
     */
    public function setPatientReports(bool $patientReports)
    {
        $this->patientReports = $patientReports;

        return $this->setVisibility(! $patientReports);
    }
}
