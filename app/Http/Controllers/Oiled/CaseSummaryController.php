<?php

namespace App\Http\Controllers\Oiled;

use App\Domain\OptionsStore;
use App\Domain\Patients\Concerns\RetrievesTreatmentLogs;
use App\Domain\Patients\ExamOptions;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class CaseSummaryController extends Controller
{
    use RetrievesTreatmentLogs;

    public function __invoke(ExamOptions $examOptions): Response
    {
        OptionsStore::merge($examOptions);

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('vitals', 'eventProcessing');
        $logs = $this->getTreatmentLogs($admission->patient, Auth::user(), (settings('logOrder') === 'desc'));

        return Inertia::render('Oiled/CaseSummary', [
            'logs' => $logs,
            'media' => fn () => $admission->patient->getMedia(),
        ]);
    }
}
