<?php

namespace App\Reporting\Contracts;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Generator
{
    /**
     * The report to generate.
     *
     * @var \App\Reporting\Contracts\Report
     */
    protected $report;

    /**
     * The local path to the generated report.
     *
     * @var string
     */
    public $filePath;

    /**
     * Constructor.
     *
     * @param  \App\Reporting\Contracts\Report  $report
     */
    public function __construct(Report $report)
    {
        $this->report = $report;

        Cache::forget('GeneratedReports.'.$this->report->team->id);
    }

    /**
     * Generate the report.
     *
     * @return Generator
     */
    abstract public function handle();

    /**
     * Make a unique directory path to the report file.
     *
     * @return string
     */
    protected function dirname()
    {
        return "reports/{$this->report->team->id}/".Carbon::now()->format('Y_m_d_H_i_s_u').'/';
    }

    /**
     * Make the report's file basename.
     *
     * @return string
     */
    public function basename()
    {
        return Str::slug($this->report->title());
    }

    /**
     * Get the fully qualified path name.
     *
     * @return string
     */
    public function fqpn()
    {
        return storage_path("app/$this->filePath");
    }

    /**
     * Copy the generated report to storage.
     *
     * @return void
     */
    // public function copyReportToStorage()
    // {
    //     $contents = Storage::get($this->filePath);

    //     if (! is_null($contents)) {
    //         Storage::disk('s3-accounts')->put(
    //             $this->filePath,
    //             $contents
    //         );
    //     }
    // }

    /**
     * Create a temporary URL for the generated report file.
     *
     * @return string
     */
    public function temporaryUrl()
    {
        //$this->copyReportToStorage();

        //Storage::delete($this->filePath);

        try {
            return Storage::temporaryUrl(
                $this->filePath,
                now()->addMinutes(30)
            );
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * Create a download response for the generated report file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        return Storage::download($this->filePath);

        //$this->copyReportToStorage();

        if (app()->runningUnitTests()) {
            return $this->fqpn();
        }

        //Response::download()
        return response()->download($this->fqpn())->deleteFileAfterSend();
    }
}
