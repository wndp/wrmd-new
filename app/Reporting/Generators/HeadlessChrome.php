<?php

namespace App\Reporting\Generators;

use Api2Pdf\Api2Pdf;
use App\PdfApiInterface;
use App\Reporting\Contracts\Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class HeadlessChrome extends Generator
{
    use GeneratesPdfs;

    // https://www.api2pdf.com/documentation/advanced-options-headless-chrome
    public const OPTIONS = [
        'landscape',
        'displayHeaderFooter',
        'printBackground',
        'scale',
        'paperWidth',
        'paperHeight',
        'marginBottom',
        'marginLeft',
        'marginRight',
        'marginTop',
        'pageRanges',
        'ignoreInvalidPageRanges',
        'headerTemplate',
        'footerTemplate',
        'preferCSSPageSize',
    ];

    /**
     * Generate the report.
     */
    public function handle(): void
    {
        $this->filePath = $this->dirname().$this->basename().'.pdf';

        $apiClient = app(PdfApiInterface::class);

        $html = View::make($this->report->viewPath(), $this->report->viewData())->render();

        $result = $apiClient->chromeHtmlToPdf($html, false, $this->basename().'.pdf', Arr::only(
            $this->mergeOptionsWithDefaults(),
            self::OPTIONS
        ));

        // $apiClient->setFilename($this->basename().'.pdf');

        // $apiClient->setOptions(Arr::only(
        //     $this->mergeOptionsWithDefaults(),
        //     self::OPTIONS
        // ));

        // $result = $apiClient->headlessChromeFromHtml($html);

        Storage::put($this->filePath, file_get_contents($result->getFile()));
    }
}
