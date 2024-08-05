<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientAnalyticsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        return Inertia::render('Patients/Analytics');
    }
}
