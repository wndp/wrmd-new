<?php

namespace App\Services;

use Api2Pdf\Api2PdfInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\File;

class DomPdfEngine implements Api2PdfInterface
{
    private $filename;

    // This should return something that has the
    // same interface as \Api2Pdf\Api2PdfResult
    public function chromeUrlToPdf($url, $inline = true, $filename = null, $options = null)
    {
        $this->filename = $filename;

        $options = new Options;
        $options->setIsRemoteEnabled(true);

        $dompdf = new Dompdf($options);

        $dompdf->setHttpContext(stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ]));

        $dompdf->loadHtmlFile($url);

        return $this->response($dompdf);
    }

    // This should return something that has the
    // same interface as \Api2Pdf\Api2PdfResult
    public function chromeHtmlToPdf($html, $inline = true, $filename = null, $options = null)
    {
        $this->filename = $filename;

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);

        return $this->response($dompdf);
    }

    public function chromeUrlToImage($url, $inline = true, $filename = null, $options = null) {}

    public function chromeHtmlToImage($html, $inline = true, $filename = null, $options = null) {}

    public function wkUrlToPdf($url, $inline = true, $filename = null, $options = null, $enableToc = false) {}

    public function wkHtmlToPdf($html, $inline = true, $filename = null, $options = null, $enableToc = false) {}

    public function libreOfficeAnyToPdf($url, $inline = true, $filename = null) {}

    public function libreOfficeThumbnail($url, $inline = true, $filename = null) {}

    public function libreOfficePdfToHtml($url, $inline = true, $filename = null) {}

    public function libreOfficeHtmlToDocx($url, $inline = true, $filename = null) {}

    public function libreOfficeHtmlToXlsx($url, $inline = true, $filename = null) {}

    public function pdfsharpMerge($urls, $inline = true, $filename = null) {}

    public function pdfsharpAddBookmarks($url, $bookmarks, $inline = true, $filename = null) {}

    public function pdfsharpAddPassword($url, $userpassword, $ownerpassword = null, $inline = true, $filename = null) {}

    public function utilityDelete($responseId) {}

    private function response($dompdf)
    {
        $dompdf->setPaper('8.5x11', 'portrait');
        $dompdf->render();

        return new class($dompdf->output(), $this->filename)
        {
            public function __construct(private string $file, private string $filename)
            {
                //
            }

            public function getFile(): string
            {
                File::ensureDirectoryExists(storage_path('app/testing'));

                File::put(storage_path("app/testing/{$this->filename}"), $this->file);

                return storage_path("app/testing/{$this->filename}");
            }
        };
    }
}
