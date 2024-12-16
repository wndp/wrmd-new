<?php

namespace App\Http\Controllers;

use App\Enums\AttributeOptionName;
use App\Models\AttributeOption;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RescuerController extends Controller
{
    /**
     * Show the form for updating a rescuer.
     */
    public function __invoke(): Response
    {
        $admission = $this->loadAdmissionAndSharePagination();

        if (! ($teamIsInPossession = $admission->patient->team_possession_id === Auth::user()->current_team_id)) {
            $admission->load('patient.possession');
        }

        OptionsStore::add([
            new LocaleOptions,
            AttributeOption::getDropdownOptions([
                AttributeOptionName::PERSON_ENTITY_TYPES->value,
            ]),
        ]);

        return Inertia::render('Patients/Rescuer', [
            'patient' => $admission->patient,
            'rescuer' => $admission->patient->rescuer->loadCount('patients'),
        ]);
    }
}
