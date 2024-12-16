<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;

class RecapController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Display the import results view.
     */
    public function index(): Response
    {
        $numRecords = $this->getImportCollectionForSession(session('import.sessionId'), session('import.filePath'))->count();
        $hours = format_number_significant_figures($numRecords / ImportFrequency::RECORDS_PER_CHUNK / ImportFrequency::BATCHES_PER_CHUNK, 2);
        $timeToProcess = Carbon::now()->addSeconds(3600 * $hours)->diffForHumans(null, true);

        return Inertia::render('Maintenance/Importing/Recap', compact('timeToProcess'));
    }
}
