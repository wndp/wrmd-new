<?php

namespace App\Http\Controllers;

use App\Domain\Locality\LocaleOptions;
use App\Domain\OptionsStore;
use App\Domain\People\PeopleOptions;
use Inertia\Inertia;
use Inertia\Response;

class RescuerController extends Controller
{
    /**
     * Show the form for updating a rescuer.
     */
    public function __invoke(PeopleOptions $peopleOptions, LocaleOptions $localeOptions): Response
    {
        OptionsStore::merge($peopleOptions);
        OptionsStore::merge($localeOptions);

        $admission = $this->loadAdmissionAndSharePagination();

        return Inertia::render('Patients/Rescuer', [
            'rescuer' => $admission->patient->rescuer->loadCount('patients'),
        ]);
    }
}
