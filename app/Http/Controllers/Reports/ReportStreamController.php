<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportStreamController extends Controller
{
    /**
     * Stream a report url response.
     * Consider as an alternative: https://gist.github.com/fideloper/6ada632650d8677ba23963ab4eae6b48
     */
    public function __invoke(): StreamedResponse
    {
        // Generate an unexpired temporary url for the requested report.
        $url = Storage::temporaryUrl(
            $this->extractPath(request('url')),
            now()->addMinutes(5)
        );

        return response()->stream(function () use ($url) {
            if ($stream = fopen($url, 'r')) {
                while (! feof($stream)) {
                    echo fread($stream, 1024);
                }
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => request('disposition', 'inline'),
            'Cache-Control' => 'no-cache',
            'Pragma' => 'no-cache',
            'Expires' => 'Sun, 20 Jan 1985 00:00:00 GMT',
        ]);
    }

    /**
     * Extract the report's storage path from the provided url.
     *
     * @return string
     */
    public function extractPath(string $url)
    {
        return ltrim(parse_url(base64_decode($url), PHP_URL_PATH), '/');
    }
}
