<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Models\Team;
use App\Reporting\Contracts\Generator;
use App\Reporting\Contracts\Report;
use App\Reporting\GeneratedReports;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReportGeneratorController extends Controller
{
    /**
     * Get a listing of the generated reports.
     */
    public function index(?Team $team = null): JsonResponse
    {
        return response()->json(
            GeneratedReports::get($team ?: Auth::user()->currentTeam)
        );
    }

    /**
     * Generate the requested report and stream it for download.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ReportRequest $request, string $namespace)
    {
        if ($request->format === 'html') {
            return $request->report($namespace)->view();
        }

        return $this->generate(
            $request->report($namespace)->shouldNotQueue(),
            $request->format
        )->download();
    }

    /**
     * Generate the requested report.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ReportRequest $request, string $key)
    {
        $this->generate(
            $request->report($key)->fromDevice($request->cookie('device-uuid')),
            $request->format
        );

        return response()->noContent();
    }

    /**
     * Generate the requested report.
     */
    private function generate(Report $report, string $format): Generator
    {
        switch ($format) {
            case 'xlsx':
                return $report->export();
                break;

            case 'pdf':
                return $report->pdf();
                break;

            case 'zpl':
                return $report->zpl();
                break;
        }
    }
}
