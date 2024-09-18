<?php

namespace App\Reporting\Contracts;

abstract class ZebraReport extends Report
{
    /**
     * Get the path to the reports view file.
     */
    public function viewPath(): string
    {
        return 'reports.reports.no-preview';
    }

    /**
     * The WAN IP address the zebra server is located at.
     */
    abstract public function ipAddress(): string;

    /**
     * The WAN port the zebra server is located at.
     */
    abstract public function port(): string;
}
