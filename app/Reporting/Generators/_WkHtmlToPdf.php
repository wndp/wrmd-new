<?php

namespace App\Reporting\Generators;

use App\Reporting\Contracts\Generator;
use App\Reporting\Contracts\PdfApiInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class WkHtmlToPdf extends Generator
{
    use GeneratesPdfs;

    // https://www.api2pdf.com/documentation/advanced-options-wkhtmltopdf
    public const OPTIONS = [
        'dpi',
        'grayscale',
        'imageDpi',
        'imageQuality',
        'lowquality',
        'marginBottom',
        'marginLeft',
        'marginRight',
        'marginTop',
        'orientation',
        'pageHeight',
        'pageSize',
        'pageWidth',
        'title',
        'pageOffset',
        'footerCenter',
        'footerFontName',
        'footerFontSize',
        'footerHtml',
        'footerLeft',
        'footerLine',
        'noFooterLine',
        'footerRight',
        'footerSpacing',
        'headerCenter',
        'headerFontName',
        'headerFontSize',
        'headerHtml',
        'headerLeft',
        'headerLine',
        'noHeaderLine',
        'headerRight',
        'headerSpacing',
    ];

    /**
     * Generate the report.
     */
    public function handle(): void
    {
        $this->filePath = $this->dirname().$this->basename().'.pdf';

        $apiClient = app(PdfApiInterface::class);
        $apiClient->setFilename($this->basename().'.pdf');
        $apiClient->setOptions(Arr::only(
            $this->mergeOptionsWithDefaults(),
            self::OPTIONS
        ));

        $html = View::make($this->report->viewPath(), $this->report->viewData())->render();
        $result = $apiClient->wkHtmlToPdfFromHtml($html);

        Storage::put($this->filePath, file_get_contents($result->getPdf()));
    }
}
