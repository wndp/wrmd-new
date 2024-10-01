<?php

namespace App\Http\Controllers\Oiled;

use App\Enums\AttributeOptionName;
use App\Enums\MediaCollection;
use App\Http\Controllers\Controller;
use App\Models\AttributeOption;
use App\Options\LocaleOptions;
use App\Repositories\OptionsStore;
use Inertia\Inertia;
use Inertia\Response;

class ProcessingController extends Controller
{
    public function __invoke(): Response
    {

        $admission = $this->loadAdmissionAndSharePagination();
        $admission->patient->load('rescuer', 'eventProcessing');

        OptionsStore::add(
            new LocaleOptions(),
            AttributeOption::getDropdownOptions([
                AttributeOptionName::OILED_PROCESSING_CONDITIONS->value,
                AttributeOptionName::OILED_PROCESSING_STATUSES->value,
                AttributeOptionName::OILED_PROCESSING_OILING_PERCENTAGES->value,
                AttributeOptionName::OILED_PROCESSING_OILING_DEPTHS->value,
                AttributeOptionName::OILED_PROCESSING_OILING_LOCATIONS->value,
                AttributeOptionName::OILED_PROCESSING_EVIDENCES->value,
                AttributeOptionName::OILED_PROCESSING_CARCASS_CONDITIONS->value,
                AttributeOptionName::OILED_PROCESSING_EXTENT_OF_SCAVENGINGS->value,
                AttributeOptionName::OILED_PROCESSING_OIL_CONDITION->value,
                AttributeOptionName::PATIENT_DISPOSITIONS->value,
                AttributeOptionName::PATIENT_RELEASE_TYPES->value,
                AttributeOptionName::PATIENT_TRANSFER_TYPES->value,
            ])
        );

        return Inertia::render('Oiled/Processing', [
            'patient' => $admission->patient,
            'media' => fn () => $admission->patient->getMedia(MediaCollection::EVIDENCE_PHOTO->value),
        ]);
    }
}
