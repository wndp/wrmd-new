<?php

namespace App\Reporting\Engines;

use Api2Pdf\ApiResult;
use Illuminate\Support\Facades\File;

class NullPdfEngine
{
    public function isInline(): bool {}

    public function setInline(bool $inline) {}

    public function getFilename(): ?string {}

    public function setFilename(?string $filename) {}

    public function getOptions(): array {}

    public function setOptions(array $options) {}

    public function wkHtmlToPdfFromHtml(string $html)
    {
        return new static;
    }

    public function headlessChromeFromHtml($html)
    {
        return (new ApiResult)->createFromResponse(json_encode([
            'success' => true,
            'pdf' => base_path('tests/storage/report.pdf'),
            'mbIn' => null,
            'mbOut' => null,
            'cost' => null,
            'responseId' => null,
        ]));
    }

    public function getPdf()
    {
        return tap(storage_path('/app/fake.pdf'), function ($file) {
            File::put($file, 'contents');
        });
    }
}
