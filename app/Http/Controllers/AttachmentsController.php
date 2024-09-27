<?php

namespace App\Http\Controllers;

use App\Enums\Extension;
use App\Support\ExtensionManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AttachmentsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        abort_unless(
            ExtensionManager::isActivated(Extension::ATTACHMENTS),
            Response::HTTP_FORBIDDEN
        );

        $admission = $this->loadAdmissionAndSharePagination();

        if (! ($teamIsInPossession = $admission->patient->team_possession_id === Auth::user()->current_team_id)) {
            $admission->load('patient.possession');
        }

        return Inertia::render('Patients/Attachments', [
            'patient' => $admission->patient,
            'media' => fn () => $admission->patient->getMedia(),
        ]);
    }
}
