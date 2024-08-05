<?php

namespace App\Http\Controllers;

use App\Domain\Classifications\CategorizationOfClinicalSigns;
use App\Domain\Classifications\CircumstancesOfAdmission;
use App\Domain\Classifications\ClinicalClassifications;
use App\Domain\Locality\LocaleOptions;
use App\Domain\Options;
use App\Domain\OptionsStore;
use App\Domain\Patients\ExamOptions;
use Inertia\Inertia;
use Inertia\Response;

class InitialCareController extends Controller
{
    /**
     * Show the form for updating the initial care.
     */
    public function __invoke(
        ExamOptions $examOptions,
        LocaleOptions $localeOptions,
    ): Response {
        OptionsStore::merge($examOptions);
        OptionsStore::merge($localeOptions);

        $admission = $this->loadAdmissionAndSharePagination();
        $exam = $admission->patient->intake_exam;

        if (settings('showTags')) {
            OptionsStore::merge([
                'circumstancesOfAdmission' => Options::arrayToSelectable(app(CircumstancesOfAdmission::class)::terms()),
                'clinicalClassifications' => Options::arrayToSelectable(app(ClinicalClassifications::class)::terms()),
                'categorizationOfClinicalSigns' => Options::arrayToSelectable(app(CategorizationOfClinicalSigns::class)::terms()),
            ]);
        }

        return Inertia::render('Patients/Initial', compact('exam'));
    }
}
