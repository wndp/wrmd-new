<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportStreamController extends Controller
{
    /**
     * Stream a report url response.
     */
    public function __invoke(Request $request): StreamedResponse
    {
        $path = parse_url(base64_decode($request->input('url')), PHP_URL_PATH);
        $path = Str::replaceStart('/'.config('filesystems.disks.s3.bucket'), '', $path);

        return Storage::response($path);
    }
}
