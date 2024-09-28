<?php

namespace App\Http\Controllers\BandingMorphometrics;

use App\Enums\AttributeOptionName;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Repositories\OptionsStore;
use Inertia\Inertia;

class BandingMorphometricsController extends Controller
{
    public function __invoke()
    {
        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load(['banding', 'morphometric']);

        OptionsStore::add([
            AttributeOption::getDropdownOptions([
                AttributeOptionName::BANDING_AGE_CODES->value,
                AttributeOptionName::BANDING_HOW_AGED_CODES->value,
                AttributeOptionName::BANDING_SEX_CODES->value,
                AttributeOptionName::BANDING_HOW_SEXED_CODES->value,
                AttributeOptionName::BANDING_STATUS_CODES->value,
                AttributeOptionName::BANDING_ADDITIONAL_INFORMATION_STATUS_CODES->value,
                AttributeOptionName::BANDING_BAND_SIZES->value,
                AttributeOptionName::BANDING_DISPOSITION_CODES->value,
                AttributeOptionName::BANDING_RECAPTURE_DISPOSITION_CODES->value,
                AttributeOptionName::BANDING_PRESENT_CONDITION_CODES->value,
                AttributeOptionName::BANDING_HOW_PRESENT_CONDITION_CODES->value,
                AttributeOptionName::BANDING_SAMPLES_COLLECTED->value,
                AttributeOptionName::BANDING_AUXILLARY_MARKER_TYPE_CODES->value,
                AttributeOptionName::BANDING_AUXILLARY_COLOR_CODES->value,
                AttributeOptionName::BANDING_AUXILLARY_CODE_COLOR->value,
                AttributeOptionName::BANDING_AUXILLARY_SIDE_OF_BIRD->value,
                AttributeOptionName::BANDING_PLACEMENT_ON_LEG->value,
            ])
        ]);

        return Inertia::render('Patients/BandingMorphometrics/Edit', [
            'patient' => $admission->patient
        ]);
    }
}
