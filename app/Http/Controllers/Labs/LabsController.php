<?php

namespace App\Http\Controllers\Labs;

use App\Domain\OptionsStore;
use App\Extensions\ExtensionNavigation;
use App\Extensions\Labs\LabOptions;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class LabsController extends Controller
{
    public function __invoke(LabOptions $options): Response
    {
        OptionsStore::merge($options);

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('labs');
        ExtensionNavigation::emit('patient', $admission);

        return Inertia::render('Patients/Labs/Index');
    }
}
