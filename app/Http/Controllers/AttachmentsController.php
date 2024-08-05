<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AttachmentsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $admission = $this->loadAdmissionAndSharePagination();

        return Inertia::render('Patients/Attachments', [
            'media' => fn () => $admission->patient->getMedia(),
        ]);
    }
}
