<?php

namespace App\Http\Controllers\Research;

use App\Domain\OptionsStore;
use App\Extensions\ExtensionNavigation;
use App\Extensions\Research\ResearchOptions;
use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ResearchController extends Controller
{
    public function __invoke(ResearchOptions $researchOptions)
    {
        OptionsStore::add($researchOptions);

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load(['banding', 'morphometric']);
        ExtensionNavigation::emit('patient', $admission);

        return Inertia::render('Patients/Research/Edit');
    }
}
