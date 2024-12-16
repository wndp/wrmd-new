<?php

namespace App\Jobs;

use App\Events\ReportGenerated;
use App\Events\ReportGenerating;
use App\Reporting\Contracts\Report;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class QueuedReportGeneration implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Report $report, public $format)
    {
        $this->report = $report;
        $this->format = $format;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ReportGenerating::dispatch(
            $this->report->deviceUuid,
            $this->report->key(),
            $this->report->title()
        );

        $generator = call_user_func_array([$this->report->shouldNotQueue(), $this->format], []);

        ReportGenerated::dispatch(
            $this->report->deviceUuid,
            $this->report->key(),
            $generator->temporaryUrl(),
            $this->format
        );
    }
}
